<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class PlaylistController extends Controller
{
    public function index()
    {
        try {
            // Retrieve the API token from the session
            $token = session('api_token');
            
            // Ensure token is available
            if (!$token) {
                $error = 'No API token found in session.';
                return view('playlists.index', compact('error'));
            }

            // Get the authenticated user's ID
            $user = auth()->user();
            $apiBase = rtrim(config('app.api_url'), '/');

            // Make the API request to fetch the playlists for the authenticated user
            $response = Http::withToken($token)
                ->get("$apiBase/user/{$user->id}/playlists");

            // If the request fails
            if ($response->failed()) {
                $playlists = collect();
                $error = "Failed to fetch playlists: " . ($response->json('message') ?? 'Unknown error');
            } else {
                // Map the response to a collection of playlists
                $data = $response->json();
                $playlists = collect($data['playlists'] ?? [])->map(fn($playlist) => (object) $playlist);
                $error = null;
            }
        } catch (\Exception $e) {
            // Handle exceptions
            $playlists = collect();
            $error = "Error fetching playlists: " . $e->getMessage();
        }

        // Return the view with playlists or error
        return view('playlists.index', compact('playlists', 'error'));
    }
}
