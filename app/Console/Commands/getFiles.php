<?php

namespace App\Console\Commands;

use App\Factuur;
use App\Services\Snelstart;
use Illuminate\Console\Command;

class getFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pdf(receipt) to Snelstart and get base64(invoice) from Snelstart';

    private $facturen;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->facturen = (new Factuur())->where('snelstart_id', '!=', '')->whereNotNull('bon_id')->whereNull(
            'base64'
        )->get();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $i = 0;

        foreach ($this->facturen as $factuur) {

            $cron = $factuur->getProject->getCompany->id;

            $i++;
            $verkooporder = (new Snelstart($cron))->getRow('verkooporders/' . $factuur->snelstart_id);
            $verkoopfactuur = (new Snelstart($cron))->getRow(
                'verkoopfacturen/' . $verkooporder['verkoopfactuur']['id']
            );
            if (array_key_exists('verkoopBoeking', $verkoopfactuur)) {
                $verkoopboeking = (new Snelstart($cron))->getRow(
                    'verkoopboekingen/' . $verkoopfactuur['verkoopBoeking']['id']
                );
                if (array_key_exists('0', $verkoopboeking['btw'])) {
                    $factuur->btw = $verkoopboeking['btw'][0]['btwBedrag'];
                }
                if (array_key_exists('0', $verkoopboeking['documents'])) {
                    $doc = (new Snelstart($bedrijf))->getRow('documenten/' . $verkoopboeking['documents'][0]['id']);
                    $factuur->base64 = $doc['content'];
                    $factuur->nummer = $verkoopboeking['factuurnummer'];
                    $factuur->update();
                }
            }
        }
    }
}
