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

            if ($response->status() === 409) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Playlist already exists.');
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

    public function songs($playlistId)
    {
        $user = auth()->user();
        $token = session('api_token');
        $apiBase = rtrim(config('app.api_url'), '/');

        if (!$token) {
            return redirect()->route('playlists.index')
                ->with('error', 'Missing API token.');
        }

        try {
            // Fetch current playlist
            $responsePlaylist = Http::withToken($token)
                ->get("$apiBase/user/{$user->id}/playlist/{$playlistId}");

            // Fetch songs in the playlist
            $responseSongs = Http::withToken($token)
                ->get("$apiBase/user/{$user->id}/playlist/{$playlistId}/songs");

            // Fetch all user playlists
            $responseUserPlaylists = Http::withToken($token)
                ->get("$apiBase/user/{$user->id}/playlists");

            if ($responsePlaylist->failed() || $responseSongs->failed() || $responseUserPlaylists->failed()) {
                $playlist = null;
                $songs = collect();
                $userPlaylists = collect();
                $error = "Failed to fetch playlist, songs, or user playlists.";
            } else {
                // Current playlist
                $playlistData = $responsePlaylist->json()['playlist'] ?? null;
                $playlist = (object)[
                    'id' => $playlistId,
                    'name' => $playlistData['name'] ?? 'Unknown Playlist',
                ];

                // Songs in current playlist
                $songsData = $responseSongs->json()['songs'] ?? [];
                $songs = collect($songsData)->map(function ($song) use ($user, $token, $apiBase) {

                    // Fetch playlists for THIS song
                    $responseSongPlaylists = Http::withToken($token)
                        ->get("$apiBase/user/{$user->id}/song/{$song['id']}/playlists");
                
                    $playlistIds = [];
                
                    if ($responseSongPlaylists->successful()) {
                        $playlistIds = collect($responseSongPlaylists->json()['playlists'] ?? [])
                            ->pluck('id')
                            ->toArray();
                    }
                
                    return (object)[
                        'id' => $song['id'] ?? null,
                        'name' => $song['name'] ?? 'Unknown Song',
                        'artist_name' => $song['album']['artist']['name'] ?? 'Unknown Artist',
                        'artist_id' => $song['album']['artist']['id'] ?? null,
                        'album_name' => $song['album']['name'] ?? 'Unknown Album',
                        'album_cover' => $song['album']['cover'] ?? asset('image/default_album.png'),
                        'album_id' => $song['album']['id'] ?? null,
                        'playlist_ids' => $playlistIds,
                    ];
                });

                $allPlaylistsData = $responseUserPlaylists->json()['playlists'] ?? [];
                $userPlaylists = collect($allPlaylistsData)
                    ->map(fn($p) => (object)[
                        'id' => $p['id'],
                        'name' => $p['name'] ?? 'Unnamed Playlist'
                    ]);

                $error = null;
            }
        } catch (\Exception $e) {
            $playlist = null;
            $songs = collect();
            $userPlaylists = collect();
            $error = "Error fetching playlist, songs, or user playlists: " . $e->getMessage();
        }

        // Send all variables to Blade
        return view('playlists.songs', compact('playlist', 'songs', 'userPlaylists', 'error'));
    }



    public function removeSong($playlistId, $songId)
    {
        $user = auth()->user();
        $token = session('api_token');
        $apiBase = rtrim(config('app.api_url'), '/');

        if (!$token) {
            return redirect()->back()
                ->with('error', 'Missing API token.');
        }

        try {
            $response = Http::withToken($token)
                ->delete("$apiBase/user/{$user->id}/playlist/{$playlistId}/song/{$songId}");

            if ($response->successful() || $response->status() === 410) {
                $data = $response->json();
                $message = $data['message'] ?? 'Song removed from playlist.';
                return redirect()->back()->with('success', $message);
            }

            $data = $response->json();
            $message = $data['message'] ?? 'Failed to remove song from playlist.';
            return redirect()->back()->with('error', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'API communication error: ' . $e->getMessage());
        }
    }

    public function syncSongPlaylists(Request $request, $songId)
    {
        $user = auth()->user();
        $token = session('api_token');
        $apiBase = rtrim(config('app.api_url'), '/');

        Http::withToken($token)->post(
            "$apiBase/user/{$user->id}/song/{$songId}/playlists",
            [
                'playlists' => $request->playlists ?? []
            ]
        );
        return back()->with('success', 'Playlists updated!');
    }
}
