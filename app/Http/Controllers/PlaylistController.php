<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlaylistController extends Controller
{
    public function index()
    {
        try {
            $token = session('api_token');

            if (!$token) {
                $error = 'No API token found in session.';
                $playlists = collect();
                return view('playlists.index', compact('playlists', 'error'));
            }

            $user = auth()->user();
            $apiBase = rtrim(config('app.api_url'), '/');

            $response = Http::withToken($token)
                ->get("$apiBase/user/{$user->id}/playlists");

            if ($response->failed()) {
                $playlists = collect();
                $error = "Failed to fetch playlists: " . ($response->json('message') ?? 'Unknown error');
            } else {
                $data = $response->json();
                $playlists = collect($data['playlists'] ?? [])
                    ->map(fn($playlist) => (object) $playlist);
                $error = null;
            }
        } catch (\Exception $e) {
            $playlists = collect();
            $error = "Error fetching playlists: " . $e->getMessage();
        }

        return view('playlists.index', compact('playlists', 'error'));
    }

    public function create()
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');
        $user = auth()->user();

        if (!$token) {
            return redirect()->route('playlists.index')
            ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $responsePlaylists = Http::withToken($token)->get("$apiBase/user/{$user->id}/playlist");

            if ($responsePlaylists->failed()) {
                $playlists = collect();
                $error = "Failed to fetch playlists.";
            } else {
                $playlistsData = $responsePlaylists->json()['playlists'] ?? [];
                $playlists = collect($playlistsData)->map(fn($playlist) => (object) $playlist);
                $error = null;
            }

            return view('playlists.playlist_create', compact('playlists', 'error'));
        } catch (\Exception $e) {
            return redirect()->route('playlists.index')
                            ->with('error', 'Error fetching playlists: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('playlists.index')
                            ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)
                            ->post("$apiBase/user/{$user->id}/playlist", [
                                'name' => $validated['name'],
                            ]);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['message'] ?? 'Playlist created successfully!';
                return redirect()->route('playlists.index')->with('success', $message);
            }

            $data = $response->json();
            $msg = $data['message'] ?? 'Unable to create playlist.';
            return redirect()->back()->withInput()->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                            ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $token = session('api_token');
        $apiBase = rtrim(config('app.api_url'), '/');

        if (!$token) {
            return redirect()->route('playlists.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)
                ->delete("$apiBase/user/{$user->id}/playlist/{$id}");

            if ($response->successful() || $response->status() === 410) {
                $data = $response->json();
                $message = $data['message'] ?? 'Playlist deleted successfully!';
                return redirect()->route('playlists.index')
                    ->with('success', $message);
            }

            $data = $response->json();
            $message = $data['message'] ?? 'Failed to delete playlist.';
            return redirect()->route('playlists.index')
                ->with('error', $message);

        } catch (\Exception $e) {
            return redirect()->route('playlists.index')
                ->with('error', 'API communication error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $token = session('api_token');
        $apiBase = rtrim(config('app.api_url'), '/');

        if (!$token) {
            return redirect()->route('playlists.index')
                ->with('error', 'Missing API token.');
        }

        try {
            $response = Http::withToken($token)
                ->get("$apiBase/user/{$user->id}/playlist/{$id}");

            if ($response->failed()) {
                return redirect()->route('playlists.index')
                    ->with('error', 'Failed to fetch playlist.');
            }

            $playlist = (object) $response->json()['playlist'];

            return view('playlists.playlist_edit', compact('playlist'));

        } catch (\Exception $e) {
            return redirect()->route('playlists.index')
                ->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $token = session('api_token');
        $apiBase = rtrim(config('app.api_url'), '/');

        try {
            $response = Http::withToken($token)
                ->patch("$apiBase/user/{$user->id}/playlist/{$id}", [
                    'name' => $request->name,
                ]);

            if ($response->successful()) {
                return redirect()->route('playlists.index')
                    ->with('success', 'Playlist updated successfully!');
            }

            return back()->with('error', 'Failed to update playlist.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
