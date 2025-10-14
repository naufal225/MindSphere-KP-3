<?php

namespace App\Http\Services;

use App\Exports\UserProgressExport;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;
use Exception;

class UserProgressExportService
{
    protected $progressService;

    public function __construct(UserProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Export user progress report for all classes
     */
    public function exportUserProgressReport(array $filters = [])
    {
        try {
            $classes = SchoolClass::all();

            if ($classes->isEmpty()) {
                throw new Exception('Tidak ada kelas yang ditemukan.');
            }

            $zipFileName = $this->generateZipFileName($filters);
            $zipPath = storage_path('app/temp/' . $zipFileName);

            // Ensure temp directory exists
            if (!file_exists(dirname($zipPath))) {
                if (!mkdir(dirname($zipPath), 0755, true)) {
                    throw new Exception('Gagal membuat direktori temporary.');
                }
            }

            $zip = new ZipArchive;
            $zipStatus = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if ($zipStatus !== TRUE) {
                throw new Exception("Gagal membuat file ZIP. Error code: " . $zipStatus);
            }

            $hasFiles = false;

            foreach ($classes as $class) {
                try {
                    $classFilters = array_merge($filters, ['class_id' => $class->id]);
                    $data = $this->progressService->getUserProgressData($classFilters);

                    if ($data->isEmpty()) {
                        continue;
                    }

                    $fileName = $this->generateExcelFileName($class, $filters);
                    $export = new UserProgressExport($data, $class->name);

                    // Generate Excel content
                    $excelContent = \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);

                    // Add to zip
                    if ($zip->addFromString($fileName, $excelContent)) {
                        $hasFiles = true;
                    } else {
                        throw new Exception("Gagal menambahkan file {$fileName} ke ZIP.");
                    }

                } catch (Exception $e) {
                    // Skip class if error, but continue with others
                    continue;
                }
            }

            $zip->close();

            // Jika tidak ada file yang ditambahkan, hapus zip yang kosong
            if (!$hasFiles) {
                if (file_exists($zipPath)) {
                    unlink($zipPath);
                }
                throw new Exception('Tidak ada data yang dapat diexport dengan filter yang dipilih.');
            }

            // Verify file was created successfully
            if (!file_exists($zipPath)) {
                throw new Exception('File ZIP gagal dibuat.');
            }

            return $zipPath;

        } catch (Exception $e) {
            // Clean up any partial files
            if (isset($zipPath) && file_exists($zipPath)) {
                unlink($zipPath);
            }
            throw new Exception('Export gagal: ' . $e->getMessage());
        }
    }

    /**
     * Generate zip file name
     */
    private function generateZipFileName(array $filters)
    {
        $startDate = $filters['start_date'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $filters['end_date'] ?? Carbon::now()->format('Y-m-d');

        return 'user-progress-report_' . $startDate . '_' . $endDate . '.zip';
    }

    /**
     * Generate Excel file name for a class
     */
    private function generateExcelFileName($class, $filters)
    {
        $startDate = $filters['start_date'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $filters['end_date'] ?? Carbon::now()->format('Y-m-d');
        $className = preg_replace('/[^a-zA-Z0-9_-]/', '_', $class->name);

        return "user-progress-report_{$className}_{$startDate}-{$endDate}.xlsx";
    }

    /**
     * Clean up temporary files
     */
    public function cleanupTempFiles($filePath)
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Clean up old temp files (older than 1 hour)
        $files = glob(storage_path('app/temp/*'));
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 3600) {
                    unlink($file);
                }
            }
        }
    }
}
