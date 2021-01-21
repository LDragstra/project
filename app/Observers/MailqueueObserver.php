<?php

namespace App\Observers;

use App\Facturen_statuslog;
use App\Mailqueue;
use Auth;

class MailqueueObserver
{
    private $log;
    private $message;

    public function __construct()
    {
        $this->log = new Facturen_statuslog();
        $this->message = 'Factuur verstuurd naar de klant';
    }

    /**
     * Handle the mailqueue "created" event.
     *
     * @param Mailqueue $mailqueue
     * @param Facturen_statuslog $log
     * @return void
     */
    public function created(Mailqueue $mailqueue)
    {
        $factuurId = str_replace('snelstartFactuur/', '', $mailqueue->initiator);

        if(!$mailqueue->mail_to == config('ToMail')) {
            $this->message = 'Cronjob';
        }

        $this->log->create(
            [
                'factuur_id' => $factuurId,
                'opmerking' => $this->message,
                'datumtijd' => now(),
                'door' => Auth::user()->naam
            ]
        );
    }

    /**
     * Handle the mailqueue "updated" event.
     *
     * @param Mailqueue $mailqueue
     * @return void
     */
    public function updated(Mailqueue $mailqueue)
    {
        //
    }

    /**
     * Handle the mailqueue "deleted" event.
     *
     * @param Mailqueue $mailqueue
     * @return void
     */
    public function deleted(Mailqueue $mailqueue)
    {
        //
    }

    /**
     * Handle the mailqueue "restored" event.
     *
     * @param Mailqueue $mailqueue
     * @return void
     */
    public function restored(Mailqueue $mailqueue)
    {
        //
    }

    /**
     * Handle the mailqueue "force deleted" event.
     *
     * @param Mailqueue $mailqueue
     * @return void
     */
    public function forceDeleted(Mailqueue $mailqueue)
    {
        //
    }
}
