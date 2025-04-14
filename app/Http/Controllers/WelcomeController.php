<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\UserModel;

class WelcomeController extends Controller
{
    public function index()
    {
        $breadcrumbs = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];
        $activeMenu = 'dashboard';
        $user = Auth::user();

        return view('welcome', [
            'breadcrumb' => $breadcrumbs,
            'activeMenu' => $activeMenu,
            'user' => $user
        ]);
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (!$request->hasFile('profile_photo')) {
            return back()->with('error', 'File tidak ditemukan di request!');
        }

        try {
            $user = UserModel::find(Auth::id());

            if (!$user) {
                return back()->with('error', 'User tidak ditemukan!');
            }

            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $file = $request->file('profile_photo');
            $filename = 'profile_' . $user->user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');

            $user->update(['profile_photo' => $path]);

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal mengunggah foto profil: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunggah foto profil.');
        }
    }
}
