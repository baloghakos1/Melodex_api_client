<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SongController extends Controller
{
    public function index(string $artist_id, string $album_id)
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            $responseAlbum = Http::get("$apiBase/artist/$artist_id/album/$album_id");
            $responseArtist = Http::get("$apiBase/artist/$artist_id");
            $responseSongs = Http::get("$apiBase/artist/$artist_id/album/$album_id/songs");

            if ($responseAlbum->failed() || $responseArtist->failed() || $responseSongs->failed()) {
                $artist = null;
                $album = null;
                $songs = collect();
                $error = "Failed to fetch artist, album, or songs";
            } else {
                $artistData = $responseArtist->json()['artist'] ?? null;
                $albumData = $responseAlbum->json()['album'] ?? null;
                $songsData = $responseSongs->json();

                $artist = (object)[
                    'id'          => $artist_id,
                    'name'        => $artistData['name'] ?? 'Unknown Artist',
                    'image'       => $artistData['image'] ?? asset('image/default_artist.png'),
                    'description' => $artistData['description'] ?? '',
                    'nationality' => $artistData['nationality'] ?? null,
                    'is_band'     => $artistData['is_band'] ?? 'no',
                ];

                $album = (object)[
                    'id'        => $album_id,
                    'name'      => $albumData['name'] ?? 'Unknown Album',
                    'cover'     => $albumData['cover'] ?? asset('image/default_album.png'),
                    'year'      => $albumData['year'] ?? '',
                    'genre'     => $albumData['genre'] ?? null,
                    'artist_id' => $artist_id,
                ];

                $songs = collect($songsData['songs'] ?? [])
                    ->map(fn($song) => (object)$song);

                $error = null;
            }

        } catch (\Exception $e) {
            $artist = null;
            $album = null;
            $songs = collect();
            $error = "Error fetching artist, album, or songs: " . $e->getMessage();
        }

        return view('artists.songs', compact('artist', 'album', 'songs', 'error'));
    }
}
