<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Services\CategoryService;
use App\Models\Category;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $categories = $this->service->getAll($request, 10);
            $stats = $this->service->getCategoryStats();

            return view('admin.categories.index', compact('categories', 'stats'));
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data kategori: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('admin.categories.create');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated());
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        try {
            $category = $this->service->findById($id);
            return view('admin.categories.show', compact('category'));
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menampilkan kategori: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        try {
            $category = $this->service->findById($id);

            return view('admin.categories.edit', compact('category'));
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memuat kategori untuk diedit: ' . $e->getMessage());
        }
    }

    public function update(CategoryRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->service->delete($id);
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }


    public function showHabits(Category $category)
    {
        $habits = $category->habits()->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.categories.habits', compact('category', 'habits'));
    }

    public function showChallenges(Category $category)
    {
        $challenges = $category->challenges()->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.categories.challenges', compact('category', 'challenges'));
    }

    public function showReflections(Category $category)
    {
        $reflections = $category->reflections()->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.categories.reflections', compact('category', 'reflections'));
    }
}
