<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolClassRequest;
use App\Http\Services\SchoolClassService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolClassController extends Controller
{
    public function __construct(private SchoolClassService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $search = $request->get('search');
            $classes = $this->service->getAll($search);

            $totalStudents = User::where('role', Role::SISWA)->count();

            return view('admin.school_classes.index', compact('classes', 'search', 'totalStudents'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            $teachers = \App\Models\User::where('role', 'guru')->get();
            return view('admin.school_classes.create', compact('teachers'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function store(SchoolClassRequest $request)
    {
        try {
            $this->service->create($request->validated());
            return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        try {
            $class = $this->service->findById($id);
            return view('admin.school_classes.show', compact('class'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $class = $this->service->findById($id);
            $teachers = \App\Models\User::where('role', 'guru')->get();
            return view('admin.school_classes.edit', compact('class', 'teachers'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(SchoolClassRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
