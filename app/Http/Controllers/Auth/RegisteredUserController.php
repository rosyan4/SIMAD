<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OpdUnit;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Hanya admin utama yang bisa register user baru
        if (!auth()->check() || !auth()->user()->isAdminUtama()) {
            abort(403, 'Hanya admin utama yang dapat mengakses halaman ini.');
        }

        $opdUnits = OpdUnit::orderBy('kode_opd_numeric')->get();

        return view('auth.register', [
            'opdUnits' => $opdUnits,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Hanya admin utama yang bisa register user baru
        if (!auth()->check() || !auth()->user()->isAdminUtama()) {
            abort(403, 'Hanya admin utama yang dapat mendaftarkan user baru.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin_utama,admin_opd'],
            'opd_unit_id' => ['nullable', 'required_if:role,admin_opd', 'exists:opd_units,opd_unit_id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'opd_unit_id' => $request->role === 'admin_opd' ? $request->opd_unit_id : null,
        ]);

        event(new Registered($user));

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil didaftarkan.');
    }
}