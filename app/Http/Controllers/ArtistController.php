<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ArtistController extends Controller
{
    public function index()
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');
            $response = Http::get("$apiBase/artists");

            if ($response->failed()) {
                $artists = collect();
                $error = "Failed to fetch artists: " . ($response->json('message') ?? 'Unknown error');
            } else {
                $data = $response->json();
                $artists = collect($data['artists'] ?? [])->map(fn($artist) => (object) $artist);
                $error = null;
            }
        } catch (\Exception $e) {
            $artists = collect();
            $error = "Error fetching artists: " . $e->getMessage();
        }

        return view('artists.index', compact('artists', 'error'));
    }

    public function show(string $artist_id)
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            $responseAlbums = Http::get("$apiBase/artist/$artist_id/albums");
            $responseArtist = Http::get("$apiBase/artist/$artist_id");

            if ($responseAlbums->failed() || $responseArtist->failed()) {
                $artist = null;
                $albums = collect();
                $error = "Failed to fetch artist or albums.";
            } else {

                $albumsData = $responseAlbums->json();
                $artistData = $responseArtist->json()['artist'] ?? null;

                $artist = $artistData ? (object)[
                    'id'    => $artist_id,
                    'name'  => $artistData['name'] ?? $albumsData['artist'] ?? 'Unknown Artist',
                    'image' => $artistData['image'] ?? asset('image/default_artist.png'),
                    'description' => $artistData['description'] ?? '',
                    'nationality' => $artistData['nationality'] ?? null,
                ] : null;

                $albums = collect($albumsData['albums'] ?? [])
                    ->map(fn($album) => (object) $album);

                $error = null;
            }

        } catch (\Exception $e) {
            $artist = null;
            $albums = collect();
            $error = "Error fetching artist: " . $e->getMessage();
        }

        return view('artists.show', compact('artist', 'albums', 'error'));
    }

    public function description(string $artist_id)
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            $responseArtist = Http::get("$apiBase/artist/$artist_id");
            $artistData = $responseArtist->json()['artist'] ?? null;

            if (!$artistData) {
                $artist = (object)[
                    'id' => $artist_id,
                    'name' => 'Unknown Artist',
                    'image' => asset('image/default_artist.png'),
                    'description' => '',
                    'nationality' => null,
                ];
                $error = "Failed to fetch artist data.";
            } else {
                $artist = (object)[
                    'id' => $artistData['id'] ?? $artist_id,
                    'name' => $artistData['name'] ?? 'Unknown Artist',
                    'image' => $artistData['image'] ?? asset('image/default_artist.png'),
                    'description' => $artistData['description'] ?? '',
                    'nationality' => $artistData['nationality'] ?? null,
                ];

                $error = null;
            }
        } catch (\Exception $e) {
            $artist = (object)[
                'id' => $artist_id,
                'name' => 'Unknown Artist',
                'image' => asset('image/default_artist.png'),
                'description' => '',
                'nationality' => null,
            ];
            $error = "Error fetching artist: " . $e->getMessage();
        }

        return view('artists.description', compact('artist', 'error'));
    }
}   
