<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function selectRole(): View
    {
        abort_unless(app()->environment(['local', 'testing']), 404);

        return view('auth-role-selector');
    }

    public function assumeRole(Request $request, string $role): RedirectResponse
    {
        abort_unless(app()->environment(['local', 'testing']), 404);

        abort_unless(in_array($role, ['dispatcher', 'supervisor', 'administrator'], true), 404);

        $user = User::query()
            ->where('role', $role)
            ->firstOrFail();

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dispatch.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('role.select');
    }
}
