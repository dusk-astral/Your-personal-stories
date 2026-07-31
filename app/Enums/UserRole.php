<?php

namespace App\Enums;

enum UserRole: string
{
    case Moderator = 'moderator';
    case User = 'user';
    case Admin = 'admin';
}
