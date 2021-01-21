<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Mailqueue extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'mailqueue';

    protected $dates = [
        'date_created',
    ];

    public function setQueue($mailTo, $factuurId, $text = 0)
    {
        if (!session('company')) {
            session(['company' => Auth::user()->getBedrijfsData]);
        }

        $factuur = Factuur::find($factuurId);

        $attachments = [];
        if ($factuur->map) {
            $attachments[] = '/path/' . $factuur->map;
        }
        if ($factuur->getBon) {
            if ($factuur->getBon->map && $factuur->getBon->map !== 'Geen bon nodig' && $factuur->getBon->map != 'Samengevoegd met de factuur') {
                $attachments[] = '/path/' . $factuur->getBon->map;
            }
        }

        $bijlagen = serialize($attachments);

        $data = "<html><style>body {background-color:#fff;font-family:'Prompt', sans-serif;'Segoe UI', 'Segoe WP', 'Segoe Regular', sans-serif;font-size:14px;line-height:1.7em;color:#000000;} </style><body>";
        $data .= nl2br($text);
        $data .= "<br><br>Met vriendelijke groet,<br/><br/>Name</body></html>";
        $data = htmlspecialchars_decode($data);

        return $this->create([
            'mail_from' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
            'mail_to' => $mailTo,
            'initiator' => 'snelstartFactuur/' . $factuurId,
            'mail_body' => $data,
            'mail_attachments' => $bijlagen,
            'mail_header' => 'Factuur ' . $factuur->nummer . ' ' . $factuur->getProject->projectnaam . ' - ' . session('company')['naam'],
            'date_created' => date('Y-m-d H:i:s'),
            'date_scheduled' => '0000-00-00 00:00:00'
        ]);
    }
}
