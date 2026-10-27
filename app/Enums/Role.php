<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Client = 'client';

    public function canAccessAdmin(): bool
    {
        return in_array($this, [self::Admin, self::Editor], true);
    }
}
