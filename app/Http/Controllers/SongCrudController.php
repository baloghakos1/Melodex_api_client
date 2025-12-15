<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SongCrudController extends Controller
{
    public function index()
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            $responseSongs = Http::get("$apiBase/songs");

            $responseAlbums = Http::get("$apiBase/albums");

            if ($responseSongs->failed() || $responseAlbums->failed()) {
                $songs = collect();
                $albums = collect();
                $error = "Failed to fetch songs or albums.";
            } else {
                $songsData = $responseSongs->json()['songs'] ?? [];
                $songs = collect($songsData)
                    ->map(fn($song) => (object) $song);

                $albumsData = $responseAlbums->json()['albums'] ?? [];
                $albums = collect($albumsData)
                    ->map(fn($album) => (object) $album)
                    ->keyBy('id');

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

    public function destroy($id)
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');
            $token = session('api_token');

            if (!$token) {
                return redirect()
                    ->route('crud.songs')
                    ->with('error', 'Missing API token — authentication failed.');
            }

            $response = Http::withToken($token)->delete("$apiBase/song/$id");

            if ($response->successful() || $response->status() === 410) {
                $data = json_decode($response->body());

                $message = $data->message ?? "Song $id was successfully deleted!";

                return redirect()
                    ->route('crud.songs')
                    ->with('success', $message);
            }

            $data = json_decode($response->body());
            $msg = $data->message ?? 'Unable to delete the song.';

            return redirect()
                ->route('crud.songs')
                ->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()
                ->route('crud.songs')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }
}
