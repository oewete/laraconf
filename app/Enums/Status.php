<?php

namespace App\Enums;

enum Status: string
{
    case Draft = 'Draft';
    case Pending = 'Pending';
    case Approved = 'Aprroved';

}
