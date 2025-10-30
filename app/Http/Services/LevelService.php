<?php
/**
 * ============================================
 * 📘 XP LEVEL REFERENCE TABLE (AUTO CALCULATED)
 * ============================================
 * Formula dasar:
 *   XP total = (level ^ 2) * 100
 *   Artinya: XP naik secara kuadrat (semakin tinggi level, semakin besar selisih XP)
 *
 * Level progression example:
 *
 * | Level | Total XP Required |
 * | ------| ----------------- |
 * | 1     | 0                 |
 * | 2     | 100               |
 * | 3     | 400               |
 * | 4     | 900               |
 * | 5     | 1600              |
 * | 6     | 2500              |
 * | 7     | 3600              |
 * | 8     | 4900              |
 * | 9     | 6400              |
 * | 10    | 8100              |
 * | 11    | 10000             |
 * | 12    | 12100             |
 * | 13    | 14400             |
 * | 14    | 16900             |
 * | 15    | 19600             |
 * | 16    | 22500             |
 * | 17    | 25600             |
 * | 18    | 28900             |
 * | 19    | 32400             |
 * | 20    | 36100             |
 * | 21    | 40000             |
 * | 22    | 44100             |
 * | 23    | 48400             |
 * | 24    | 52900             |
 * | 25    | 57600             |
 * | 26    | 62500             |
 * | 27    | 67600             |
 * | 28    | 72900             |
 * | 29    | 78400             |
 * | 30    | 84100             |
 * | 31    | 90000             |
 * | 32    | 96100             |
 * | 33    | 102400            |
 * | 34    | 108900            |
 * | 35    | 115600            |
 * | 36    | 122500            |
 * | 37    | 129600            |
 * | 38    | 136900            |
 * | 39    | 144400            |
 * | 40    | 152100            |
 * | 41    | 160000            |
 * | 42    | 168100            |
 * | 43    | 176400            |
 * | 44    | 184900            |
 * | 45    | 193600            |
 * | 46    | 202500            |
 * | 47    | 211600            |
 * | 48    | 220900            |
 * | 49    | 230400            |
 * | 50    | 240100            |
 *
 * Catatan:
 * - XP total berarti jumlah XP kumulatif yang dibutuhkan untuk mencapai level tersebut.
 * - XP per level = (level^2 - (level-1)^2) * 100
 *   Contoh:
 *     Level 5 → 1600 total XP
 *     Level 4 → 900 total XP
 *     Jadi butuh 700 XP untuk naik dari level 4 ke 5.
 *
 * Sistem ini cocok untuk:
 * - Gamifikasi edukasi
 * - Habit tracking dengan reward progresif
 * - Growth system berbasis refleksi dan challenge
 */

namespace App\Http\Services;

class LevelService
{
    /**
     * Dapatkan level user berdasarkan total XP.
     * Formula kuadrat: semakin tinggi level, semakin sulit naik.
     */
    public static function getLevelFromXp(int $xp): int
    {
        // contoh kurva xp: level 1 = 0 XP, level 2 = 100 XP, level 3 = 400 XP, dst.
        return floor(sqrt($xp / 100)) + 1;
    }

    /**
     * Dapatkan total XP yang dibutuhkan untuk mencapai level berikutnya.
     */
    public static function getXpForNextLevel(int $level): int
    {
        // XP total untuk mencapai level tertentu (bukan tambahan)
        return pow($level, 2) * 100;
    }

    /**
     * Cek apakah user naik level setelah penambahan XP.
     * Jika iya, update level user-nya.
     */
    public static function updateUserLevel($user): void
    {
        $newLevel = self::getLevelFromXp($user->xp);

        if ($newLevel > $user->level) {
            $user->level = $newLevel;
            $user->save();
        }
    }
}
