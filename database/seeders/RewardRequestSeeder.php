<?php

namespace Database\Seeders;

use App\Models\Reward;
use App\Models\RewardRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class RewardRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $rewards = Reward::where('is_active', true)->orderBy('id')->get()->keyBy('name');
        $students = User::where('role', 'siswa')
            ->orderByDesc('coin')
            ->orderBy('id')
            ->get();
        $usedStudentIds = [];

        $this->createRewardRequest(
            status: RewardRequest::STATUS_PENDING,
            admin: $admin,
            reward: $rewards['Buku Tulis Eksklusif'],
            student: $this->pickStudent($students, $usedStudentIds, 120),
            quantity: 1
        );

        $this->createRewardRequest(
            status: RewardRequest::STATUS_APPROVED,
            admin: $admin,
            reward: $rewards['Voucher Kantin'],
            student: $this->pickStudent($students, $usedStudentIds, 180),
            quantity: 1
        );

        $this->createRewardRequest(
            status: RewardRequest::STATUS_COMPLETED,
            admin: $admin,
            reward: $rewards['Merchandise KeepItGrow'],
            student: $this->pickStudent($students, $usedStudentIds, 220),
            quantity: 1
        );

        $this->createRewardRequest(
            status: RewardRequest::STATUS_REJECTED,
            admin: $admin,
            reward: $rewards['Template Portofolio Digital'],
            student: $this->pickStudent($students, $usedStudentIds, 140),
            quantity: 1,
            rejectionReason: 'Stok dialokasikan untuk kegiatan sekolah.'
        );
    }

    private function pickStudent($students, array &$usedStudentIds, int $minimumCoin): User
    {
        $student = $students
            ->first(function (User $candidate) use ($usedStudentIds, $minimumCoin) {
                return !in_array($candidate->id, $usedStudentIds, true)
                    && $candidate->coin >= $minimumCoin;
            });

        if (!$student) {
            throw new \RuntimeException("Tidak ada siswa demo dengan koin minimal {$minimumCoin}.");
        }

        $usedStudentIds[] = $student->id;

        return $student;
    }

    private function createRewardRequest(
        string $status,
        User $admin,
        Reward $reward,
        User $student,
        int $quantity = 1,
        ?string $rejectionReason = null
    ): void {
        $request = RewardRequest::createWithSnapshot([
            'user_id' => $student->id,
            'reward_id' => $reward->id,
            'quantity' => $quantity,
        ]);

        if ($status === RewardRequest::STATUS_APPROVED) {
            $request->approve($admin);
        }

        if ($status === RewardRequest::STATUS_COMPLETED) {
            $request->approve($admin);
            $request->complete($admin);
        }

        if ($status === RewardRequest::STATUS_REJECTED) {
            $request->reject($admin, $rejectionReason ?? 'Request ditolak admin.');
        }
    }
}
