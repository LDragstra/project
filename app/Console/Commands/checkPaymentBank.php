<?php

namespace App\Console\Commands;

use App\Factuur;
use App\Mailqueue;
use App\Services\Snelstart;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class checkPaymentBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paymentsFS:grekening';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if payment(s) where made on the G-rekening.';

    private $companies;

    private $grekBetalingen;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->companies = [1, 2, 3];

        $this->grekBetalingen = [];
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
                    "bankboekingen?\$filter=Datum ge DateTime'" . Carbon::yesterday()->format('Y-m-d\TH:i:s') . "'"
                );
                if ($data) {
                    foreach ($data as $item) {
                        if ($item['verkoopboekingBoekingsRegels']) {
                            foreach ($item['verkoopboekingBoekingsRegels'] as $verkoopboeking) {
                                $factuurnr = (new Snelstart($company))->getRow(
                                    "verkoopboekingen/" . $verkoopboeking['boekingId']['id']
                                );
                                $factuur = Factuur::where('nummer', '=', $factuurnr['factuurnummer'])->first();
                                if ($verkoopboeking['credit'] < ($factuur->bedrag * 0.3) && $factuur->grekeningSaldo == 0) {
                                    $factuur->grekeningSaldo = $verkoopboeking['credit'];
                                    $factuur->update();
                                    $this->grekBetalingen[] = [
                                        'factuurnummer' => $factuur->nummer,
                                        'bedrag' => $verkoopboeking['credit']
                                    ];
                                }
                            }
                        }
                    }
                }
            } catch (Throwable $th) {
                (new Mailqueue())->create(
                    [
                        'mail_from' => config('mail.from.address'),
                        'mail_from_name' => 'Cronjob',
                        'mail_to' => config('mail.ToMail'),
                        'initiator' => 'factuurBetalingFout',
                        'mail_body' => 'Er is iets fout gegaan bij het automatisch verwerken van facturen - btw of g-rekening. <br>' . $th,
                        'mail_attachments' => '',
                        'mail_header' => 'Fout in cron aflettering',
                        'date_created' => date('Y-m-d H:i:s'),
                        'date_scheduled' => '0000-00-00 00:00:00'
                    ]
                );
            }
        }

        if (!empty($this->grekBetalingen)) {
            $body = 'De volgende facturen hebben vandaag/gister een g-rekening betaling ontvangen: <br><br>';
            foreach ($this->grekBetalingen as $grekBetaling) {
                $body .= $grekBetaling['factuurnummer'] . '<br> bedrag: &euro;' . $grekBetaling['bedrag'] . '<br><br>';
            }
            (new Mailqueue())->create(
                [
                    'mail_from' => config('mail.from.address'),
                    'mail_from_name' => 'FireStop',
                    'mail_to' => config('mail.ToMail'),
                    'initiator' => 'grekBetaling',
                    'mail_body' => $body,
                    'mail_attachments' => '',
                    'mail_header' => 'Betaalde g-rekening facturen ' . date('d-m-Y'),
                    'date_created' => date('Y-m-d H:i:s'),
                    'date_scheduled' => '0000-00-00 00:00:00'
                ]
            );
        }
    }
}
