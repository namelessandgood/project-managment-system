<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'Pending';
    case Accepted = 'Accepted';
    case Rejected = 'Rejected';
}