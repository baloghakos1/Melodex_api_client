<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

class AlbumCrudController extends Controller
{
    public function index()
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            $responseAlbums = Http::get("$apiBase/albums");

            $responseArtists = Http::get("$apiBase/artists");

            if ($responseAlbums->failed() || $responseArtists->failed()) {
                $albums = collect();
                $artists = collect();
                $error = "Failed to fetch albums or artists.";
            } else {
                $albumsData = $responseAlbums->json()['albums'] ?? [];
                $albums = collect($albumsData)
                    ->map(fn($album) => (object) $album);

                $artistsData = $responseArtists->json()['artists'] ?? [];
                $artists = collect($artistsData)
                    ->map(fn($artist) => (object) $artist)
                    ->keyBy('id');

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

    public function destroy($id)
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');
            $token = session('api_token');

            if (!$token) {
                return redirect()
                    ->route('crud.albums')
                    ->with('error', 'Missing API token — authentication failed.');
            }

            $response = Http::withToken($token)->delete("$apiBase/album/$id");

            if ($response->successful() || $response->status() === 410) {
                $data = json_decode($response->body());

                $message = $data->message ?? "Album $id was successfully deleted!";

                return redirect()
                    ->route('crud.albums')
                    ->with('success', $message);
            }

            $data = json_decode($response->body());
            $msg = $data->message ?? 'Unable to delete the album.';

            return redirect()
                ->route('crud.albums')
                ->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()
                ->route('crud.albums')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

}
