<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipApproved extends Notification
{
    use Queueable;

    protected $membershipDetails;

    protected $qrCodeBase64;

    /**
     * Create a new notification instance.
     */
    public function __construct($membershipDetails, $qrCodeBase64)
    {
        $this->membershipDetails = $membershipDetails;
        $this->qrCodeBase64 = $qrCodeBase64;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your EZ Fitness Membership is Approved!')
            ->view('emails.membership-qr', [
                'name' => $this->membershipDetails['name'],
                'email' => $this->membershipDetails['email'],
                'plan' => $this->membershipDetails['plan'],
                'plan_price' => $this->membershipDetails['plan_price'],
                'plan_duration' => $this->membershipDetails['plan_duration'],
                'subscription' => $this->membershipDetails['subscription'],
                'subscription_price' => $this->membershipDetails['subscription_price'],
                'subscription_duration' => $this->membershipDetails['subscription_duration'],
                'end_date' => $this->membershipDetails['end_date'],
                'qr_code_base64' => $this->qrCodeBase64,
            ]);
    }
}
