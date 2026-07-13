<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:ensure-docker-admin', function () {
    $username = 'superadmin';
    $email = 'superadmin@example.com';

    $user = User::where('username', $username)
        ->orWhere('email', $email)
        ->orWhere('username', 'admin123')
        ->orWhere('email', 'admin123@example.com')
        ->first();

    $data = [
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'username' => $username,
        'email' => $email,
        'password' => Hash::make('Super123Admin'),
        'role' => 'super_admin',
        'status' => 'active',
        'email_verified_at' => now(),
    ];

    if ($user) {
        $user->forceFill($data)->save();
        $this->info('Docker super admin account updated.');

        return;
    }

    User::create($data);
    $this->info('Docker super admin account created.');
})->purpose('Ensure the default Docker super admin account exists');
