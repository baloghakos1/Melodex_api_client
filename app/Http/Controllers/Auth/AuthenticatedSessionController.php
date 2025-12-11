<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use App\Models\User;

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
    // Call the API login endpoint
    $response = Http::api()->post('/user/login', [
        'email' => $request->email,
        'password' => $request->password,
    ]);

    if ($response->successful()) {
        $responseBody = json_decode($response->body());

        // Check if the API returned a user
        if (empty($responseBody->user)) {
            return back()->withErrors([
                'message' => 'Hibás bejelentkezési adatok.'
            ])->withInput();
        }

        // Store API token and user info in session using request
        $request->session()->put([
            'api_token' => $responseBody->user->token,
            'user_id' => $responseBody->user->id,
            'user_email' => $responseBody->user->email,
        ]);

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        // Create a temporary User instance and log in
        $user = new User();
        $user->id = $responseBody->user->id;
        $user->email = $responseBody->user->email;

        Auth::login($user);

        // Redirect to intended page or dashboard
        return redirect()->intended('/dashboard');
    }

    // API login failed (wrong credentials, server error, etc.)
    return back()->withErrors([
        'message' => 'Hibás bejelentkezési adatok.'
    ])->withInput();
}
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log out Laravel user
        Auth::logout();

        // Clear session data
        $request->session()->forget([
            'api_token',
            'user_id',
            'user_email',
        ]);

        // Optionally, regenerate session ID for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login page or home
        return redirect('/');
    }
}
