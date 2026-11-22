<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['username' => 'marco.dc', 'full_name' => 'Marco Dela Cruz', 'email' => 'marco@example.com'],
            ['username' => 'jessa.r', 'full_name' => 'Jessa Ramos', 'email' => 'jessa@example.com'],
            ['username' => 'carlo.m', 'full_name' => 'Carlo Mendoza', 'email' => 'carlo@example.com'],
            ['username' => 'lara.a', 'full_name' => 'Lara Aquino', 'email' => 'lara@example.com'],
            ['username' => 'rico.n', 'full_name' => 'Rico Navarro', 'email' => 'rico@example.com'],
        ];

        foreach ($users as $record) {
            $user = User::firstOrNew(['username' => $record['username']]);
            $user->fill($record);

            if (! $user->password) {
                $user->password = 'StoreDemo!2026';
            }

            $user->save();
        }
    }
}
