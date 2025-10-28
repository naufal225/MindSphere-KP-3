<?php

namespace App\Enums;

enum HabitStatus: string
{
    case JOINED = 'joined';
    case SUBMITTED = 'submitted';
    case COMPLETED = 'completed';
}
