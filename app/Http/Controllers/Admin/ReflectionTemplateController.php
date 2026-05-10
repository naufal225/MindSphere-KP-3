<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReflectionTemplateRequest;
use App\Http\Services\ReflectionTemplateService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReflectionTemplateController extends Controller
{
    public function __construct(private ReflectionTemplateService $service)
    {
    }

    public function index(Request $request): View
    {
        $templates = $this->service->getAll($request);

        return view('admin.reflection_templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.reflection_templates.create');
    }

    public function store(ReflectionTemplateRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['is_active'] = $request->input('submit_mode') === 'publish';
            $template = $this->service->create($data);

            return redirect()
                ->route('admin.reflection-templates.edit', $template->id)
                ->with('success', 'Template refleksi berhasil dibuat.');
        } catch (Exception $exception) {
            return back()->withInput()->with('error', 'Gagal membuat template refleksi: ' . $exception->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $template = $this->service->findById($id);

        return view('admin.reflection_templates.edit', compact('template'));
    }

    public function update(ReflectionTemplateRequest $request, int $id): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['is_active'] = $request->input('submit_mode') === 'publish';
            $this->service->update($id, $data);

            return redirect()
                ->route('admin.reflection-templates.edit', $id)
                ->with('success', 'Template refleksi berhasil diperbarui.');
        } catch (Exception $exception) {
            return back()->withInput()->with('error', 'Gagal memperbarui template refleksi: ' . $exception->getMessage());
        }
    }

    public function duplicate(int $id): RedirectResponse
    {
        try {
            $template = $this->service->duplicate($id);

            return redirect()
                ->route('admin.reflection-templates.edit', $template->id)
                ->with('success', 'Template refleksi berhasil diduplikasi.');
        } catch (Exception $exception) {
            return back()->with('error', 'Gagal menduplikasi template refleksi: ' . $exception->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->service->delete($id);

            return redirect()
                ->route('admin.reflection-templates.index')
                ->with('success', 'Template refleksi berhasil dihapus.');
        } catch (Exception $exception) {
            return back()->with('error', 'Gagal menghapus template refleksi: ' . $exception->getMessage());
        }
    }

    public function publish(int $id): RedirectResponse
    {
        try {
            $this->service->publish($id);

            return redirect()
                ->route('admin.reflection-templates.index')
                ->with('success', 'Template refleksi berhasil diaktifkan.');
        } catch (Exception $exception) {
            return back()->with('error', 'Gagal mengaktifkan template refleksi: ' . $exception->getMessage());
        }
    }

    public function unpublish(int $id): RedirectResponse
    {
        try {
            $this->service->unpublish($id);

            return redirect()
                ->route('admin.reflection-templates.index')
                ->with('success', 'Template refleksi berhasil dinonaktifkan.');
        } catch (Exception $exception) {
            return back()->with('error', 'Gagal menonaktifkan template refleksi: ' . $exception->getMessage());
        }
    }
}
