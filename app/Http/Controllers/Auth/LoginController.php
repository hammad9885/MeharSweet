<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Show the application's login form.
     *
     * @return \Inertia\Response
     */
    public function showLoginForm()
    {
        return \Inertia\Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        try {
            if ($this->attemptLogin($request)) {
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }

                return $this->sendLoginResponse($request);
            }
        } catch (\Throwable $e) {
            return back()->withErrors([
                'email' => '⚠️ MySQL Server Offline: Port 3306 par MySQL start nahi hai. Database connect karne ke liye XAMPP/MySQL start karein.',
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        // Check if the authenticated user is an admin
        if (Auth::user()) {
            return 'admin/dashboard'; // Admin dashboard route
        }

        // Otherwise, redirect to the user dashboard
        return '/user/dashboard'; // User dashboard route
    }
    public function logout(Request $request)
    {
        Auth::logout();

        // Redirect to the desired path after logout
        return redirect('login'); // Change this as needed
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
