<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProfileController extends Controller
{
    /**
     * Display the admin's profile page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update the admin's profile information.
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|alpha_dash|min:3|max:30|unique:users,username,' . $user->id,
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->update($validated);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Profil berhasil diperbarui.');

        } catch (Exception $e) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Password berhasil diubah.');

        } catch (Exception $e) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Gagal mengubah password: ' . $e->getMessage());
        }
    }

    /**
     * Update the admin's avatar.
     */
    public function updateAvatar(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Delete old avatar if exists
            if ($user->avatar_url && Storage::exists($user->avatar_url)) {
                Storage::delete($user->avatar_url);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            $user->update([
                'avatar_url' => $avatarPath,
            ]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Foto profil berhasil diubah.');

        } catch (Exception $e) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Gagal mengubah foto profil: ' . $e->getMessage());
        }
    }

    /**
     * Delete the admin's avatar.
     */
    public function deleteAvatar()
    {
        try {
            $user = Auth::user();

            if ($user->avatar_url && Storage::exists($user->avatar_url)) {
                Storage::delete($user->avatar_url);
            }

            $user->update([
                'avatar_url' => null,
            ]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Foto profil berhasil dihapus.');

        } catch (Exception $e) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Gagal menghapus foto profil: ' . $e->getMessage());
        }
    }
}
