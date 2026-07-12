<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:ensure-docker-admin', function () {
    $username = 'admin123';
    $email = 'admin123@example.com';

    $user = User::where('username', $username)
        ->orWhere('email', $email)
        ->first();

    $data = [
        'first_name' => 'Docker',
        'last_name' => 'Admin',
        'username' => $username,
        'email' => $email,
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'status' => 'active',
        'email_verified_at' => now(),
    ];

    if ($user) {
        $user->forceFill($data)->save();
        $this->info('Docker admin account updated.');

        return;
    }

    User::create($data);
    $this->info('Docker admin account created.');
})->purpose('Ensure the default Docker admin account exists');
