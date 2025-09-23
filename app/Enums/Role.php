<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case GURU = 'guru';
    case SISWA = 'siswa';
    case ORTU = 'ortu';
}
