<?php

return [
    // Администратор, создаваемый AdminUserSeeder. Пароль задаётся только при создании.
    'email' => env('ADMIN_EMAIL'),
    'name' => env('ADMIN_NAME', 'Admin'),
    'password' => env('ADMIN_PASSWORD'),
];
