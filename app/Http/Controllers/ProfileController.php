<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $warga = null;

        if ($user?->isWarga() && !empty($user->warga_id)) {
            $warga = Warga::query()->whereKey($user->warga_id)->first();
        }

        return view('profile.index', compact('user', 'warga'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user?->isWarga()) {
            abort(403, 'Hanya akun warga yang dapat mengubah password melalui halaman ini.');
        }

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], (string) $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}

