<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberQRCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $qrCodePath;

    public function __construct($member, $qrCodePath)
    {
        $this->member = $member;
        $this->qrCodePath = $qrCodePath;
    }

    public function build()
    {
        return $this->markdown('emails.member_qr')
                    ->subject('Your Membership QR Code')
                    ->attach($this->qrCodePath, [
                        'as' => 'membership_qr.png',
                        'mime' => 'image/png',
                    ])
                    ->with([
                        'name' => "{$this->member->fname} {$this->member->lname}",
                        'email' => $this->member->email,
                    ]);
    }
}
