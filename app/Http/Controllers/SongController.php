<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SongController extends Controller
{
    public function index(string $artist_id, string $album_id)
    {
        $error = null;
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            // 🔹 Main API calls
            $responseAlbum = Http::get("$apiBase/artist/$artist_id/album/$album_id");
            $responseArtist = Http::get("$apiBase/artist/$artist_id");
            $responseSongs = Http::get("$apiBase/artist/$artist_id/album/$album_id/songs");

            // 🔥 NEW: user playlists (needed for modal)
            $user = auth()->user();

            $responseUserPlaylists = Http::withToken(session('api_token'))
                ->get("$apiBase/user/{$user->id}/playlists");

            if (
                $responseAlbum->failed() ||
                $responseArtist->failed() ||
                $responseSongs->failed() ||
                $responseUserPlaylists->failed()
            ) {
                return view('artists.songs', [
                    'artist' => null,
                    'album' => null,
                    'songs' => collect(),
                    'userPlaylists' => collect(),
                    'error' => "Failed to fetch artist, album, songs or playlists"
                ]);
            }

            // 🔹 Artist
            $artistData = $responseArtist->json()['artist'] ?? null;

            $artist = (object)[
                'id' => $artist_id,
                'name' => $artistData['name'] ?? 'Unknown Artist',
                'image' => $artistData['image'] ?? asset('image/default_artist.png'),
                'description' => $artistData['description'] ?? '',
                'nationality' => $artistData['nationality'] ?? null,
            ];

            // 🔹 Album
            $albumData = $responseAlbum->json()['album'] ?? null;

            $album = (object)[
                'id' => $album_id,
                'name' => $albumData['name'] ?? 'Unknown Album',
                'cover' => $albumData['cover'] ?? asset('image/default_album.png'),
                'year' => $albumData['year'] ?? '',
                'genre' => $albumData['genre'] ?? null,
                'artist_id' => $artist_id,
            ];

            // 🔹 User playlists (for modal checkboxes)
            $userPlaylists = collect($responseUserPlaylists->json()['playlists'] ?? [])
                ->map(fn($p) => (object)[
                    'id' => $p['id'],
                    'name' => $p['name'] ?? 'Unnamed Playlist'
                ]);

            // 🔹 Songs + playlist IDs per song (IMPORTANT)
            $songsData = $responseSongs->json();

            $songs = collect($songsData['songs'] ?? [])
                ->map(function ($song) use ($apiBase, $user) {

                    $token = session('api_token');

                    $playlistIds = [];

                    try {
                        $responseSongPlaylists = Http::withToken($token)
                            ->get("$apiBase/user/{$user->id}/song/{$song['id']}/playlists");

                        if ($responseSongPlaylists->successful()) {
                            $playlistIds = collect($responseSongPlaylists->json()['playlists'] ?? [])
                                ->pluck('id')
                                ->map(fn($id) => (int)$id)
                                ->toArray();
                        }
                    } catch (\Exception $e) {
                        $playlistIds = [];
                    }

                    return (object)[
                        'id' => $song['id'] ?? null,
                        'name' => $song['name'] ?? 'Unknown Song',
                        'playlist_ids' => $playlistIds, // 🔥 REQUIRED for checked boxes
                    ];
                });

            return view('artists.songs', compact(
                'artist',
                'album',
                'songs',
                'userPlaylists',
                'error'
            ));

        } catch (\Exception $e) {
            return view('artists.songs', [
                'artist' => null,
                'album' => null,
                'songs' => collect(),
                'userPlaylists' => collect(),
                'error' => "Error: " . $e->getMessage()
            ]);
        }
    }
}