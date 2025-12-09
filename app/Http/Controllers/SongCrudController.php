<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SongCrudController extends Controller
{
    public function index()
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            // 1. Fetch all songs
            $responseSongs = Http::get("$apiBase/songs");

            // 2. Fetch all albums
            $responseAlbums = Http::get("$apiBase/albums");

            if ($responseSongs->failed() || $responseAlbums->failed()) {
                $songs = collect();
                $albums = collect();
                $error = "Failed to fetch songs or albums.";
            } else {
                // Songs data
                $songsData = $responseSongs->json()['songs'] ?? [];
                $songs = collect($songsData)
                    ->map(fn($song) => (object) $song);

                // Albums data
                $albumsData = $responseAlbums->json()['albums'] ?? [];
                $albums = collect($albumsData)
                    ->map(fn($album) => (object) $album)
                    ->keyBy('id'); // Key by ID for easy lookup

                // Attach album object to each song
                $songs = $songs->map(function ($song) use ($albums) {
                    $song->album = $albums[$song->album_id] ?? null;
                    return $song;
                });

                $error = null;
            }

        } catch (\Exception $e) {
            $songs = collect();
            $albums = collect();
            $error = "Error fetching songs or albums: " . $e->getMessage();
        }

        return view('crud.songs', compact('songs', 'albums', 'error'));
    }

    
}
