<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\UserProgressService;
use App\Http\Services\UserProgressExportService;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Exception;

class UserProgressController extends Controller
{
    protected $progressService;
    protected $exportService;

    public function __construct(UserProgressService $progressService, UserProgressExportService $exportService)
    {
        $this->progressService = $progressService;
        $this->exportService = $exportService;
    }

    /**
     * Display user progress report
     */
    public function index(Request $request)
    {
        try {
            // Get filter parameters
            $filters = $this->getFiltersFromRequest($request);

            // Get data for display
            $userProgressData = $this->progressService->getUserProgressData($filters);

            // Get chart data
            $topStudentsChart = $this->progressService->getTopStudentsChartData($filters);
            $classComparisonChart = $this->progressService->getClassComparisonChartData($filters);
            $moodDistributionChart = $this->progressService->getMoodDistributionData($filters);

            // Get classes for filter dropdown
            $classes = $this->progressService->getClassesForFilter();

            // Apply sorting
            $userProgressData = $this->applySorting($userProgressData, $request->get('sort_by', 'xp_total'));

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return view('admin.user-progress.index', compact(
            'userProgressData',
            'topStudentsChart',
            'classComparisonChart',
            'moodDistributionChart',
            'classes',
            'filters'
        ));
    }

    /**
     * Export user progress report to Excel
     */
    public function export(Request $request)
    {
        try {
            // Get filter parameters
            $filters = $this->getFiltersFromRequest($request);

            // Generate zip file
            $zipPath = $this->exportService->exportUserProgressReport($filters);

            // Check if file exists
            if (!file_exists($zipPath)) {
                throw new Exception('File export tidak berhasil dibuat.');
            }

            // Get download file name
            $downloadFileName = $this->getExportFileName($filters);

            // Return download response
            return Response::download($zipPath, $downloadFileName, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);

        } catch (Exception $e) {
            return redirect()->route('admin.user-progress.index')
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get filters from request
     */
    private function getFiltersFromRequest(Request $request): array
    {
        return [
            'class_id' => $request->get('class_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'min_activity' => $request->get('min_activity'),
            'mood_range' => $request->get('mood_range'),
            'sort_by' => $request->get('sort_by', 'xp_total'),
            'include_inactive' => $request->boolean('include_inactive', false),
        ];
    }

    /**
     * Apply sorting to the data
     */
    private function applySorting($data, $sortBy)
    {
        try {
            $sortDirection = 'desc';

            $sortOptions = [
                'xp_total' => ['xp_total', 'desc'],
                'habits_completed' => ['habits_completed', 'desc'],
                'habit_streak' => ['habit_streak', 'desc'],
                'reflections_written' => ['reflections_written', 'desc'],
                'name' => ['user.name', 'asc'],
            ];

            if (isset($sortOptions[$sortBy])) {
                list($sortField, $sortDirection) = $sortOptions[$sortBy];

                if (str_contains($sortField, 'user.')) {
                    $data = $data->sortBy(function ($item) use ($sortField) {
                        return $item['user']->name;
                    }, SORT_REGULAR, $sortDirection === 'desc');
                } else {
                    $data = $data->sortBy($sortField, SORT_REGULAR, $sortDirection === 'desc');
                }
            }

            return $data->values();
        } catch (Exception $e) {
            throw new Exception('Gagal mengurutkan data: ' . $e->getMessage());
        }
    }

    /**
     * Generate export file name
     */
    private function getExportFileName(array $filters): string
    {
        $startDate = $filters['start_date'] ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->format('Y-m-d');

        return "user-progress-report_{$startDate}-{$endDate}.zip";
    }

    /**
     * Get chart data via AJAX
     */
    public function getChartData(Request $request)
    {
        try {
            $filters = $this->getFiltersFromRequest($request);

            $chartType = $request->get('chart_type');

            switch ($chartType) {
                case 'top_students':
                    $data = $this->progressService->getTopStudentsChartData($filters);
                    break;
                case 'class_comparison':
                    $data = $this->progressService->getClassComparisonChartData($filters);
                    break;
                case 'mood_distribution':
                    $data = $this->progressService->getMoodDistributionData($filters);
                    break;
                default:
                    $data = [];
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filter options
     */
    public function getFilterOptions()
    {
        try {
            $classes = $this->progressService->getClassesForFilter();

            $moodOptions = [
                ['value' => 'all', 'label' => 'Semua Mood'],
                ['value' => 'happy-only', 'label' => 'Hanya Senang'],
                ['value' => 'neutral+', 'label' => 'Netral ke Atas'],
            ];

            $sortOptions = [
                ['value' => 'xp_total', 'label' => 'XP Tertinggi'],
                ['value' => 'habits_completed', 'label' => 'Habit Terbanyak'],
                ['value' => 'habit_streak', 'label' => 'Streak Tertinggi'],
                ['value' => 'reflections_written', 'label' => 'Refleksi Terbanyak'],
                ['value' => 'name', 'label' => 'Nama A-Z'],
            ];

            return response()->json([
                'success' => true,
                'classes' => $classes,
                'mood_options' => $moodOptions,
                'sort_options' => $sortOptions,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
