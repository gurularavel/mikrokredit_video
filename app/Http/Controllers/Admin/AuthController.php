<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('admin_user')) {
            return redirect()->route('admin.applications.index');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['password' => 'İstifadəçi adı və ya şifrə yanlışdır.'])->withInput(['username' => $request->username]);
        }

        session()->regenerate();
        session(['admin_user' => [
            'id'       => $admin->id,
            'username' => $admin->username,
            'name'     => $admin->name,
        ]]);

        return redirect()->route('admin.applications.index');
    }

    public function logout()
    {
        session()->forget('admin_user');
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showChangePassword()
    {
        return view('admin.password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $admin = Admin::find(session('admin_user.id'));

        if (!$admin || !Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Cari şifrə yanlışdır.']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Şifrə uğurla dəyişdirildi.');
    }
}
