<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(SystemPagesSeeder::class);

        if (! app()->isProduction()) {
            $this->call(DemoContentSeeder::class);
        }
    }
}
