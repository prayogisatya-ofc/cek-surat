<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $admins = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.index', compact('admins', 'q'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'username' => ['required','string','max:50','unique:users,username'],
            'password' => ['required','string','min:6'],
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.index')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $admin)
    {
        return view('admin.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'username' => ['required','string','max:50','unique:users,username,'.$admin->id],
            'password' => ['nullable','string','min:6'],
        ]);

        $admin->name = $data['name'];
        $admin->username = $data['username'];

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return redirect()->route('admin.index')->with('success', 'Admin berhasil diupdate.');
    }

    public function destroy(User $admin)
    {
        if (Auth::id() === $admin->id) {
            return back()->with('error', 'Tidak bisa menghapus akun yang sedang login.');
        }

        $admin->delete();

        return back()->with('success', 'Admin berhasil dihapus.');
    }
}
