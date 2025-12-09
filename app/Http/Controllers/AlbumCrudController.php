<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

class AlbumCrudController extends Controller
{
    public function index()
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            // 1. Fetch all albums
            $responseAlbums = Http::get("$apiBase/albums");

            // 2. Fetch all artists
            $responseArtists = Http::get("$apiBase/artists");

            if ($responseAlbums->failed() || $responseArtists->failed()) {
                $albums = collect();
                $artists = collect();
                $error = "Failed to fetch albums or artists.";
            } else {
                // Albums data
                $albumsData = $responseAlbums->json()['albums'] ?? [];
                $albums = collect($albumsData)
                    ->map(fn($album) => (object) $album);

                // Artists data
                $artistsData = $responseArtists->json()['artists'] ?? [];
                $artists = collect($artistsData)
                    ->map(fn($artist) => (object) $artist)
                    ->keyBy('id'); // Key by ID for easy lookup

                // Attach artist object to each album
                $albums = $albums->map(function ($album) use ($artists) {
                    $album->artist = $artists[$album->artist_id] ?? null;
                    return $album;
                });

                $error = null;
            }

        } catch (\Exception $e) {
            $albums = collect();
            $artists = collect();
            $error = "Error fetching albums or artists: " . $e->getMessage();
        }

        return view('crud.albums', compact('albums', 'artists', 'error'));
    }

}
