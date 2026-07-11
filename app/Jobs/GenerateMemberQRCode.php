<?php

namespace App\Jobs;

use App\Mail\MemberQRCodeMail;
use App\Models\MemberProfile;
use App\Models\MembershipPlan;
use App\Models\Subscriptions;
use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateMemberQRCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;

    public $memberId;

    public $planId;

    public $subscriptionId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $memberId, $planId, $subscriptionId)
    {
        $this->userId = $userId;
        $this->memberId = $memberId;
        $this->planId = $planId;
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Fetch fresh data from database
            $user = User::with('branch:branch_id,name')->findOrFail($this->userId);
            $memberProfile = MemberProfile::findOrFail($this->memberId);
            $plan = MembershipPlan::findOrFail($this->planId);
            $subscription = Subscriptions::findOrFail($this->subscriptionId);

            // Generate QR code data
            $qrData = json_encode([
                'id' => $memberProfile->member_id,
                'name' => "{$user->first_name} {$user->last_name}",
                'email' => $user->email,
                'branch' => $user->branch->name ?? 'N/A',
                'plan' => [
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'start' => $memberProfile->start_date,
                    'end' => $memberProfile->end_date,
                ],
                'subscription' => [
                    'name' => $subscription->name,
                    'price' => $subscription->price,
                    'start' => $memberProfile->start_date_for_subscription,
                    'end' => $memberProfile->end_date_for_subscription,
                ],
                'status' => $memberProfile->subscription_status,
                'generated' => now()->toDateTimeString(),
            ]);

            $qrRelativePath = "qr/member_{$user->user_id}.png";
            $fullPath = storage_path("app/public/{$qrRelativePath}");

            // Create directory if it doesn't exist
            $directory = dirname($fullPath);
            if (! file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Delete old QR code if exists
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Generate QR code
            $result = Builder::create()
                ->writer(new PngWriter)
                ->data($qrData)
                ->encoding(new Encoding('UTF-8'))
                ->size(300)
                ->margin(10)
                ->build();

            $result->saveToFile($fullPath);

            if (! file_exists($fullPath)) {
                throw new \Exception('QR code file was not created');
            }

            $fileSize = filesize($fullPath);
            if ($fileSize === 0) {
                throw new \Exception('QR code file is empty');
            }

            // Update member profile with QR path
            $memberProfile->qr_code = $qrRelativePath;
            $memberProfile->save();

            Log::info('QR Code generated successfully', [
                'user_id' => $user->user_id,
                'path' => $qrRelativePath,
                'file_size' => $fileSize,
            ]);

            // Send email with QR code
            Mail::to($user->email)->send(new MemberQRCodeMail($memberProfile, $fullPath));

            Log::info("QR Code email sent to: {$user->email}");

        } catch (\Exception $e) {
            Log::error('QR Code generation job failed', [
                'user_id' => $this->userId,
                'member_id' => $this->memberId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Retry the job if it fails (max 3 attempts)
            if ($this->attempts() < 3) {
                $this->release(60); // Retry after 60 seconds
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('QR Code generation job permanently failed', [
            'user_id' => $this->userId,
            'member_id' => $this->memberId,
            'error' => $exception->getMessage(),
        ]);
    }
}
