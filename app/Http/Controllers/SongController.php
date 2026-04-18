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
            $user = auth()->user();
            $token = session('api_token');

            // API calls
            $responseAlbum = Http::get("$apiBase/artist/$artist_id/album/$album_id");
            $responseArtist = Http::get("$apiBase/artist/$artist_id");
            $responseSongs = Http::get("$apiBase/artist/$artist_id/album/$album_id/songs");

            $responseUserPlaylists = Http::withToken($token)
                ->get("$apiBase/user/{$user->id}/playlists");

            if (
                $responseAlbum->failed() ||
                $responseArtist->failed() ||
                $responseSongs->failed() ||
                $responseUserPlaylists->failed()
            ) {
                throw new \Exception("Failed to fetch required data");
            }

            // Artist
            $artistData = $responseArtist->json()['artist'] ?? null;

            $artist = (object)[
                'id' => $artist_id,
                'name' => $artistData['name'] ?? 'Unknown Artist',
                'image' => $artistData['image'] ?? asset('image/default_artist.png'),
                'description' => $artistData['description'] ?? '',
                'nationality' => $artistData['nationality'] ?? null,
            ];

            // Album
            $albumData = $responseAlbum->json()['album'] ?? null;

            $album = (object)[
                'id' => $album_id,
                'name' => $albumData['name'] ?? 'Unknown Album',
                'cover' => $albumData['cover'] ?? asset('image/default_album.png'),
                'year' => $albumData['year'] ?? '',
                'genre' => $albumData['genre'] ?? null,
                'artist_id' => $artist_id,
            ];

            // User playlists
            $userPlaylists = collect($responseUserPlaylists->json()['playlists'] ?? [])
                ->map(fn($p) => (object)[
                    'id' => $p['id'],
                    'name' => $p['name'] ?? 'Unnamed Playlist'
                ]);

            // Songs
            $songsData = $responseSongs->json();

            $songs = collect($songsData['songs'] ?? [])
                ->map(function ($song) use ($apiBase, $user, $token, $artist, $album) {

                    // Fetch playlists containing this song
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

                        // 🔥 REQUIRED FOR PLAYER
                        'stream_url' => $song['stream_url'] 
                            ?? "$apiBase/song/{$song['id']}/stream",

                        // ✅ UI DATA
                        'artist_name' => $song['artist_name'] ?? $artist->name,
                        'album_cover' => $song['album_cover'] ?? $album->cover,

                        // ✅ PLAYLIST DATA
                        'playlist_ids' => $playlistIds,
                    ];
                });

        } catch (\Exception $e) {
            $artist = null;
            $album = null;
            $songs = collect();
            $userPlaylists = collect();
            $error = "Error: " . $e->getMessage();
        }

        return view('artists.songs', compact(
            'artist',
            'album',
            'songs',
            'userPlaylists',
            'error'
        ));
    }
}
