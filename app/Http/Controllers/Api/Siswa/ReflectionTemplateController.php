<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Services\StudentReflectionTemplateService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReflectionTemplateController extends Controller
{
    public function __construct(private StudentReflectionTemplateService $service)
    {
    }

    public function active()
    {
        $context = $this->service->getActiveTemplateContext(Auth::user());

        return response()->json([
            'status' => 'success',
            'data' => $context,
        ]);
    }

    public function activeSubmission()
    {
        $context = $this->service->getActiveSubmission(Auth::user());

        return response()->json([
            'status' => 'success',
            'data' => $context,
        ]);
    }

    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'answers' => 'nullable|array',
        ]);

        try {
            $submission = $this->service->saveDraft(Auth::user(), $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Draft refleksi berhasil disimpan.',
                'data' => $submission,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        try {
            $submission = $this->service->submit(Auth::user(), $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Refleksi berhasil disubmit.',
                'data' => $submission,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function history()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getHistory(Auth::user()),
        ]);
    }

    public function showHistory(int $studentReflection)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->service->getHistoryDetail(Auth::user(), $studentReflection),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
