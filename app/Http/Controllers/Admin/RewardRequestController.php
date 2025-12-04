<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\RewardRequestService;
use App\Http\Requests\Admin\StoreRewardRequestRequest;
use App\Http\Requests\Admin\UpdateRewardRequestRequest;
use App\Models\RewardRequest;
use Illuminate\Http\Request;

class RewardRequestController extends Controller
{
    protected $rewardRequestService;

    public function __construct(RewardRequestService $rewardRequestService)
    {
        $this->rewardRequestService = $rewardRequestService;
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['status', 'search', 'date_from', 'date_to', 'type', 'sort_by', 'sort_order']);
            $requests = $this->rewardRequestService->getRequests($filters);

            // Statistics untuk dashboard
            $statistics = $this->rewardRequestService->getStatistics();

            return view('admin.reward-requests.index', compact('requests', 'statistics'));
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'Gagal memuat data request: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $request = $this->rewardRequestService->getRequestById($id);
            return view('admin.reward-requests.show', compact('request'));
        } catch (\Exception $e) {
            return redirect()->route('admin.requests.index')
                ->withErrors(['error' => 'Request tidak ditemukan: ' . $e->getMessage()]);
        }
    }

    public function approve($id, Request $request)
    {
        try {
            $approver = auth()->user();
            $rewardRequest = $this->rewardRequestService->approveRequest($id, $approver);

            return redirect()->route('admin.requests.show', $rewardRequest->id)
                ->with('success', 'Request berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyetujui request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function reject($id, Request $request)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);

            $rejector = auth()->user();
            $rewardRequest = $this->rewardRequestService->rejectRequest($id, $rejector, $request->rejection_reason);

            return redirect()->route('admin.requests.show', $rewardRequest->id)
                ->with('success', 'Request berhasil ditolak');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menolak request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function complete($id, Request $request)
    {
        try {
            $completer = auth()->user();
            $rewardRequest = $this->rewardRequestService->completeRequest($id, $completer);

            return redirect()->route('admin.requests.show', $rewardRequest->id)
                ->with('success', 'Request berhasil diselesaikan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyelesaikan request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function cancel($id)
    {
        try {
            $rewardRequest = $this->rewardRequestService->cancelRequest($id);

            return redirect()->route('admin.requests.show', $rewardRequest->id)
                ->with('success', 'Request berhasil dibatalkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal membatalkan request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function export(Request $request)
    {
        try {
            $filters = $request->only(['status', 'date_from', 'date_to', 'type']);
            return $this->rewardRequestService->exportRequests($filters);
        } catch (\Exception $e) {
            return redirect()->route('admin.requests.index')
                ->withErrors(['error' => 'Gagal export data: ' . $e->getMessage()]);
        }
    }

    public function statistics()
    {
        try {
            $statistics = $this->rewardRequestService->getStatistics();
            return response()->json($statistics);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
