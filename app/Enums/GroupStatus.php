<?php

declare(strict_types=1);

namespace App\Enums;

enum GroupStatus: string
{
    case Proposed = 'Proposed';
    case InProgress = 'InProgress';
    case ReadyForDefense = 'ReadyForDefense';
    case Evaluated = 'Evaluated';
}