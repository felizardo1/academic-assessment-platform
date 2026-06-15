<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    protected $signature   = 'app:create-test-user';
    protected $description = 'Creates a test teacher account for evaluation purposes';

    public function handle(): void
    {
        $email = 'teacher@assessment.com';

        if (User::where('email', $email)->exists()) {
            $this->warn('Test user already exists. Skipping creation.');
            $this->info('Email:    ' . $email);
            $this->info('Password: teacher123');
            return;
        }

        User::create([
            'name'              => 'Test Teacher',
            'email'             => $email,
            'password'          => Hash::make('teacher123'),
            'role'              => 'teacher',
            'email_verified_at' => now(),
        ]);

        $this->info('Test user created successfully!');
        $this->info('Email:    ' . $email);
        $this->info('Password: teacher123');
    }
}
