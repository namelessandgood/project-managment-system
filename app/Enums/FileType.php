<?php

declare(strict_types=1);

namespace App\Enums;

enum FileType: string
{
    case Pdf = 'pdf';
    case Docx = 'docx';
    case Zip = 'zip';
    case Link = 'link';
}