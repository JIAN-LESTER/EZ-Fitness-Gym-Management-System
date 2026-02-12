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
        // Ensure relationships are loaded
        $this->member->load(['user', 'plan', 'subscription']);
        
        // Verify QR code file exists
        if (!file_exists($this->qrCodePath)) {
            \Log::error("QR code file not found at: {$this->qrCodePath}");
            throw new \Exception("QR code file not found");
        }
        
        // Get file size for logging
        $fileSize = filesize($this->qrCodePath);
        \Log::info("Attaching QR code", [
            'path' => $this->qrCodePath,
            'size' => $fileSize,
            'exists' => file_exists($this->qrCodePath)
        ]);
        
        return $this->markdown('emails.member_qr')
                    ->subject('Your Membership QR Code - EZ Fitness Gym')
                    ->attach($this->qrCodePath, [
                        'as' => 'membership_qr.png',
                        'mime' => 'image/png',
                    ])
                    ->with([
                        'name' => "{$this->member->user->first_name} {$this->member->user->last_name}",
                        'email' => $this->member->user->email ?? 'N/A',
                        'plan' => $this->member->plan->name ?? 'N/A',
                        'plan_price' => $this->member->plan ? '₱' . number_format($this->member->plan->price, 2) : 'N/A',
                        'plan_duration' => $this->member->plan->duration_days ?? 'N/A',
                        'subscription' => $this->member->subscription->name ?? 'N/A',
                        'subscription_price' => $this->member->subscription ? '₱' . number_format($this->member->subscription->price, 2) : 'N/A',
                        'subscription_duration' => $this->member->subscription->duration_days ?? 'N/A',
                        'end_date' => $this->member->end_date_for_subscription 
                            ? \Carbon\Carbon::parse($this->member->end_date_for_subscription)->format('M d, Y')
                            : 'N/A',
                        'qr_code_base64' => base64_encode(file_get_contents($this->qrCodePath))
                    ]);
    }
}