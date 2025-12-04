<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRewardRequest;
use App\Http\Requests\UpdateRewardRequest;
use App\Http\Services\RewardService;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    private $rewardService;

    public function __construct(RewardService $rewardService)
    {
        $this->rewardService = $rewardService;
    }

    public function index(Request $request)
    {
        try {
            $filters = [
                'status' => $request->status,
                'type' => $request->type,
                'stock' => $request->stock,
                'search' => $request->search,
                'sort_by' => $request->sort_by,
                'sort_order' => $request->sort_order,
                'per_page' => $request->per_page ?? 10,
            ];

            $rewards = $this->rewardService->getRewards($filters);
            $statistics = $this->rewardService->getStatistics();

            return view('admin.rewards.index', compact('rewards', 'statistics'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            $types = [
                'physical' => 'Fisik (Hadiah fisik, perlu pengambilan)',
                'digital' => 'Digital (Hadiah digital, dikirim via kode)',
                'voucher' => 'Voucher (Kode voucher untuk merchant)'
            ];

            return view('admin.rewards.create', compact('types'));
        } catch (\Exception $e) {
            return redirect()->route('admin.rewards.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function store(StoreRewardRequest $request)
    {
        try {
            $validated = $request->validated();

            // Stock handling
            if ($request->has('stock_unlimited') && $request->stock_unlimited == '1') {
                $validated['stock'] = -1;
            } else {
                // Pastikan stock ada di validated data
                $validated['stock'] = $validated['stock'] ?? $request->stock;
            }

            // Tambahkan stock_unlimited ke validated data untuk diolah di service
            if ($request->has('stock_unlimited')) {
                $validated['stock_unlimited'] = $request->stock_unlimited;
            }

            $reward = $this->rewardService->createReward($validated);

            return redirect()->route('admin.rewards.index')
                ->with('success', 'Reward berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }


    public function show($id)
    {
        try {
            $reward = $this->rewardService->getRewardById($id);
            return view('admin.rewards.show', compact('reward'));
        } catch (\Exception $e) {
            return redirect()->route('admin.rewards.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $reward = Reward::findOrFail($id);
            $types = [
                'physical' => 'Fisik (Hadiah fisik, perlu pengambilan)',
                'digital' => 'Digital (Hadiah digital, dikirim via kode)',
                'voucher' => 'Voucher (Kode voucher untuk merchant)'
            ];

            return view('admin.rewards.update', compact('reward', 'types'));
        } catch (\Exception $e) {
            return redirect()->route('admin.rewards.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(UpdateRewardRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            // Stock handling
            if ($request->has('stock_unlimited') && $request->stock_unlimited == '1') {
                $validated['stock'] = -1;
            } else {
                // Jika stock ada di validated, gunakan itu
                if (isset($validated['stock'])) {
                    $validated['stock'] = $validated['stock'];
                } else if ($request->has('stock')) {
                    $validated['stock'] = $request->stock;
                }
                // Jika tidak ada keduanya, biarkan apa adanya (untuk update)
            }

            // Tambahkan stock_unlimited ke validated data
            if ($request->has('stock_unlimited')) {
                $validated['stock_unlimited'] = $request->stock_unlimited;
            }

            // Jika ada remove_image, tambahkan ke validated
            if ($request->has('remove_image') && $request->remove_image == '1') {
                $validated['remove_image'] = true;
            }

            $reward = $this->rewardService->updateReward($id, $validated);

            return redirect()->route('admin.rewards.index')
                ->with('success', 'Reward berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }


    public function destroy($id)
    {
        try {
            $this->rewardService->deleteReward($id);

            return redirect()->route('admin.rewards.index')
                ->with('success', 'Reward berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.rewards.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $reward = $this->rewardService->toggleStatus($id);
            $status = $reward->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->back()
                ->with('success', "Reward berhasil {$status}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateStock(Request $request, $id)
    {
        try {
            $request->validate([
                'stock' => 'required|integer|min:-1'
            ]);

            $reward = $this->rewardService->updateStock($id, $request->stock);

            return redirect()->back()
                ->with('success', 'Stok reward berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        try {
            $filters = [
                'status' => $request->status,
                'type' => $request->type,
                'stock' => $request->stock,
            ];

            $rewards = Reward::query();

            // Apply filters
            if ($filters['status'] === 'active') {
                $rewards->active();
            } elseif ($filters['status'] === 'inactive') {
                $rewards->where('is_active', false);
            }

            if ($filters['type']) {
                $rewards->type($filters['type']);
            }

            if ($filters['stock'] === 'available') {
                $rewards->available();
            }

            $rewards = $rewards->get();

            // Generate CSV
            $fileName = 'rewards_' . date('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$fileName}",
            ];

            $callback = function () use ($rewards) {
                $file = fopen('php://output', 'w');

                // Header
                fputcsv($file, [
                    'ID',
                    'Nama',
                    'Deskripsi',
                    'Biaya Koin',
                    'Stok',
                    'Status',
                    'Tipe',
                    'Dibuat',
                    'Terakhir Update'
                ]);

                // Data
                foreach ($rewards as $reward) {
                    fputcsv($file, [
                        $reward->id,
                        $reward->name,
                        $reward->description ?? '',
                        $reward->coin_cost,
                        $reward->stock == -1 ? 'Unlimited' : $reward->stock,
                        $reward->is_active ? 'Aktif' : 'Nonaktif',
                        $reward->type,
                        $reward->created_at->format('d/m/Y H:i'),
                        $reward->updated_at->format('d/m/Y H:i'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengekspor data: ' . $e->getMessage()]);
        }
    }
}
