<?php

namespace App\Enums;

enum ReportCategory: string
{
    case Misleading = 'misleading';
    case Satire = 'satire';
    case OutOfContext = 'out_of_context';
    case Fabricated = 'fabricated';
    case Other = 'other';
}
