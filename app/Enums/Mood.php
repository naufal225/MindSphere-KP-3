<?php

namespace App\Enums;

enum Mood: string
{
    case HAPPY = 'happy';
    case NEUTRAL = 'neutral';
    case SAD = 'sad';
    case ANGRY = 'angry';
    case TIRED = 'tired';
}
