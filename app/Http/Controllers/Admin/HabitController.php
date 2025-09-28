<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HabitRequest;
use App\Http\Services\HabitService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitController extends Controller
{
    public function __construct(private HabitService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $habits = $this->service->getAll($request, 10);
            $stats = $this->service->getHabitStats();
            $categories = $this->service->getCategories();

            return view('admin.habits.index', compact('habits', 'stats', 'categories'));
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data kebiasaan: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $categories = $this->service->getCategories();
            return view('admin.habits.create', compact('categories'));
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membuka form pembuatan kebiasaan: ' . $e->getMessage());
        }
    }

    public function store(HabitRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated());
            return redirect()->route('admin.habits.index')->with('success', 'Kebiasaan berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan kebiasaan: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $data = $this->service->findById($id);
            return view('admin.habits.show', $data);
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menampilkan detail kebiasaan: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            $habit = $this->service->findById($id)['habit'];
            $categories = $this->service->getCategories();
            return view('admin.habits.edit', compact('habit', 'categories'));
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memuat kebiasaan untuk diedit: ' . $e->getMessage());
        }
    }

    public function update(HabitRequest $request, string $id): RedirectResponse
    {
        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('admin.habits.index')->with('success', 'Kebiasaan berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui kebiasaan: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->service->delete($id);
            return redirect()->route('admin.habits.index')->with('success', 'Kebiasaan berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus kebiasaan: ' . $e->getMessage());
        }
    }
}
