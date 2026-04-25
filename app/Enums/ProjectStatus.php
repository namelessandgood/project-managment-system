<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
    case NeedsModification = 'NeedsModification';
}