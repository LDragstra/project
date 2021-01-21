<?php

namespace App\Console\Commands;

use App\Facturen_statuslog;
use App\Factuur;
use App\Mailqueue;
use App\Services\Snelstart;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class checkPaymentsFS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paymentsFS:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payed invoices from the previous days and reconcile the open invoices in VAP';

    private $companies;
    private $facturen;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->companies = [1, 2, 3];

        $this->facturen = [];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ($this->companies as $company) {
            try {
                $data = (new Snelstart($company))->getRow(
                    "verkoopfacturen?\$filter=ModifiedOn ge DateTime'" . Carbon::yesterday()->format(
                        'Y-m-d\TH:i:s'
                    ) . "'"
                );
                if ($data) {
                    foreach ($data as $item):
                        $factuur = Factuur::where('nummer', $item['factuurnummer'])->first();
                        if ($item && $factuur) {
                            if ($factuur->voldaan == 'N' && $item['openstaandSaldo'] != ($item['factuurBedrag'] or $factuur->openstaandSaldo)) {
                                $log = (new Facturen_statuslog());
                                $log->factuur_id = $factuur->id;
                                $log->opmerking = 'Deelbetaling ontvangen.';
                                $log->datumtijd = Carbon::now()->format('Y-m-d H:i:s');
                                $log->door = 'Automatisch';
                                if ($factuur->openstaandSaldo == $factuur->bedrag) {
                                    $factuur->voldaanop = Carbon::yesterday()->format('Y-m-d H:i:s');
                                }
                                $factuur->openstaandSaldo = $item['openstaandSaldo'];
                                if ($item['openstaandSaldo'] == 0):
                                    $factuur->voldaan = 'J';
                                    $log->opmerking = 'Factuur Betaald.';
                                endif;
                                $log->save();
                                $factuur->update();
                                $facturen[] = [
                                    'factuurnummer' => $factuur->nummer,
                                    'bedrag' => $paid
                                ];
                            }
                        }
                    endforeach;
                }
            } catch (Throwable $th) {
                (new Mailqueue())->create(
                    [
                        'mail_from' => config('mail.from.address'),
                        'mail_from_name' => 'Cronjob',
                        'mail_to' => config('mail.ToMail'),
                        'initiator' => 'factuurBetalingFout',
                        'mail_body' => 'Er is iets fout gegaan bij het automatisch afletteren van facturen. <br>' . $th,
                        'mail_attachments' => '',
                        'mail_header' => 'Fout in cron aflettering',
                        'date_created' => date('Y-m-d H:i:s'),
                        'date_scheduled' => '0000-00-00 00:00:00'
                    ]
                );
            }
        }
        if (!empty($facturen)) {
            $body = 'De volgende facturen zijn vandaag betaald: <br><br>';
            foreach ($facturen as $factuur) {
                $body .= $factuur['factuurnummer'] . '<br> bedrag: &euro;' . $factuur['bedrag'] . '<br><br>';
            }
            (new Mailqueue())->create(
                [
                    'mail_from' => config('mail.from.address'),
                    'mail_from_name' => 'Cronjob',
                    'mail_to' => config('mail.ToMail'),
                    'initiator' => 'factuurBetaling',
                    'mail_body' => $body,
                    'mail_attachments' => '',
                    'mail_header' => 'Betaalde facturen ' . date('d-m-Y'),
                    'date_created' => date('Y-m-d H:i:s'),
                    'date_scheduled' => '0000-00-00 00:00:00'
                ]
            );
        }
    }
}
