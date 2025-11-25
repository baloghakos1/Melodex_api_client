<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

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
        $response = Http::api()->post('/users/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if($request->successful()) {
            $responseBody = json_decode($response->body());
            if(empty($responseBody->data)) {
                return back()->withErrors([
                    'message' => $responseBody->message,
                ]);
            }
        }
        session([
            'api_token' => $responseBody->data->token,
            'user_name' => $responseBody->data->name,
            'user_email' => $responseBody->data->email,
        ]);

        return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        session()->forget('api_token');

        return redirect('/');
    }
}
