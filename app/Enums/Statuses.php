<?php

namespace App\Enums;

enum Statuses: int
{
    case NEW = 1;
    case COMPLETED = 2;
    case ARCHIVED = 3;
}
