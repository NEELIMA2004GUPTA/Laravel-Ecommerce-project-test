<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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
        $request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        if(!Auth::attempt($request->only('email','password'),$request->filled('remember'))){
            return back()->withErrors([
                'email' => 'Invalid Credentials.',
        ]);
        }

        $request->session()->regenerate();
        $user=Auth::user();
        if($user->is_blocked){
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
            'email' => 'Your account has been blocked. Contact support.',
            ]);
        }
        return redirect()->intended(
            $user->role === 'admin'
                ? route('admin.dashboard')
                : route('products.index')
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
