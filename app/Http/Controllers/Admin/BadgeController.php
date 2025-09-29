<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BadgeRequest;
use App\Http\Services\BadgeService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BadgeController extends Controller
{
    public function __construct(private BadgeService $service)
    {
    }

    public function index(Request $request): RedirectResponse|View
    {
        try {
            $badges = $this->service->getAll($request, 10);
            $categories = $this->service->getCategories();
            return view('admin.badges.index', compact('badges', 'categories'));
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data badge: ' . $e->getMessage());
        }
    }

    public function create(): RedirectResponse|View
    {
        try {
            $categories = $this->service->getCategories();
            return view('admin.badges.create', compact('categories'));
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membuka form pembuatan badge: ' . $e->getMessage());
        }
    }

    public function store(BadgeRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated());
            return redirect()->route('admin.badges.index')->with('success', 'Badge berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan badge: ' . $e->getMessage());
        }
    }

    public function show(string $id): RedirectResponse|View
    {
        try {
            $data = $this->service->findById($id);
            return view('admin.badges.show', $data);
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menampilkan detail badge: ' . $e->getMessage());
        }
    }

    public function edit(string $id): RedirectResponse|View
    {
        try {
            $badge = $this->service->findById($id)['badge'];
            $categories = $this->service->getCategories();
            return view('admin.badges.edit', compact('badge', 'categories'));
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memuat badge untuk diedit: ' . $e->getMessage());
        }
    }

    public function update(BadgeRequest $request, string $id): RedirectResponse
    {
        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('admin.badges.index')->with('success', 'Badge berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui badge: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->service->delete($id);
            return redirect()->route('admin.badges.index')->with('success', 'Badge berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus badge: ' . $e->getMessage());
        }
    }
}
