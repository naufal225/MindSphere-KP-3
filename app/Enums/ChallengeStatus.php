<?php

namespace App\Enums;

enum ChallengeStatus: string
{
    case JOINED = 'joined';
    case SUBMITTED = 'submitted';
    case COMPLETED = 'completed';
}
