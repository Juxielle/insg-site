<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        User::where('role', '!=', 'admin')->delete();

        User::updateOrCreate(['email' => 'admin@insg.ga'], [
            'name' => 'Administrateur INSG',
            'role' => 'admin',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
    }
}
