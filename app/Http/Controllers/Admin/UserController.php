<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Services\UserService;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(private UserService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $users = $this->service->getUsers($request);
            $classes = SchoolClass::with('teacher')->get(); // Ambil data kelas untuk dropdown

            return view('admin.users.index', compact('users', 'classes'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            $classes = \App\Models\SchoolClass::all();
            $parents = \App\Models\User::where('role', 'ortu')->get(); // Ambil data orang tua
            return view('admin.users.create', compact('classes', 'parents'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function store(UserRequest $request)
    {
        try {
            $this->service->createUser($request->validated());
            return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        try {
            $data = $this->service->getUserWithProgress($id);
            return view('admin.users.show', $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function edit($id)
    {
        try {
            $user = $this->service->getUserById($id);
            $classes = \App\Models\SchoolClass::all();
            $parents = \App\Models\User::where('role', 'ortu')->get(); // Ambil data orang tua
            return view('admin.users.edit', compact('user', 'classes', 'parents'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $this->service->updateUser($id, $request->validated());
            return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->deleteUser($id);
            return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
