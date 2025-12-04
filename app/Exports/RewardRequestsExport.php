<?php

namespace App\Exports;

use App\Models\RewardRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RewardRequestsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $requests;

    public function __construct($requests)
    {
        $this->requests = $requests;
    }

    public function collection()
    {
        return $this->requests;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Siswa',
            'NIS',
            'Reward',
            'Tipe',
            'Jumlah',
            'Total Koin',
            'Status',
            'Alasan Penolakan',
            'Kode',
            'Tanggal Expired Kode',
            'Tanggal Request',
            'Tanggal Disetujui',
            'Tanggal Selesai',
            'Disetujui Oleh',
            'Diselesaikan Oleh'
        ];
    }

    public function map($request): array
    {
        return [
            $request->id,
            $request->user->name ?? 'N/A',
            $request->user->nis ?? 'N/A',
            $request->reward->name ?? 'N/A',
            $request->reward->type ?? 'N/A',
            $request->quantity,
            $request->total_coin_cost,
            $request->status_label,
            $request->rejection_reason ?? '-',
            $request->code ?? '-',
            $request->code_expires_at ? $request->code_expires_at->format('d/m/Y H:i') : '-',
            $request->created_at->format('d/m/Y H:i'),
            $request->approved_at ? $request->approved_at->format('d/m/Y H:i') : '-',
            $request->completed_at ? $request->completed_at->format('d/m/Y H:i') : '-',
            $request->approver->name ?? '-',
            $request->completer->name ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => ['font' => ['bold' => true]],

            // Style untuk kolom tertentu
            'A' => ['alignment' => ['horizontal' => 'center']],
            'F' => ['alignment' => ['horizontal' => 'center']],
            'G' => ['alignment' => ['horizontal' => 'center']],
            'H' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}
