<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleName: string
{
    case Student = 'student';
    case Supervisor = 'supervisor';
    case Evaluator = 'evaluator';
    case Coordinator = 'coordinator';
    case Admin = 'admin';
}