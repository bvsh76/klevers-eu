<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD не заданы — администратор не создан.');

            return;
        }

        $user = User::firstOrNew(['email' => mb_strtolower($email)]);

        if (! $user->exists) {
            $user->name = config('admin.name');
            $user->password = $password;
        }

        $user->role = Role::Admin;
        $user->save();

        $this->command?->info("Администратор: {$user->email}");
    }
}
