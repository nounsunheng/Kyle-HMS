<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get authenticated user
        $user = Auth::user();

        // Redirect based on user role
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, Administrator!');
        } elseif ($user->hasRole('doctor')) {
            return redirect()->intended(route('doctor.dashboard'))->with('success', 'Welcome back, Dr. ' . $user->name . '!');
        } elseif ($user->hasRole('patient')) {
            return redirect()->intended(route('patient.dashboard'))->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Default redirect if no role matched
        return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
