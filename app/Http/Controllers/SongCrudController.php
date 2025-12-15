<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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

    public function create()
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('songcrud.index')
                            ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $responseAlbums = Http::withToken($token)->get("$apiBase/albums");

            if ($responseAlbums->failed()) {
                $albums = collect();
                $error = "Failed to fetch albums.";
            } else {
                $albumsData = $responseAlbums->json()['albums'] ?? [];
                $albums = collect($albumsData)->map(fn($album) => (object) $album);
                $error = null;
            }

            return view('crud.song_create', compact('albums', 'error'));
        } catch (\Exception $e) {
            return redirect()->route('songcrud.index')
                            ->with('error', 'Error fetching albums: ' . $e->getMessage());
        }
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lyrics' => 'nullable|string',
            'songwriter' => 'required|string|max:255',
            'album_id' => 'required|integer',
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('songcrud.index')
                            ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)
                            ->post("$apiBase/song", [
                                'name' => $validated['name'],
                                'lyrics' => $validated['lyrics'] ?? null,
                                'songwriter' => $validated['songwriter'],
                                'album_id' => $validated['album_id'],
                            ]);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['message'] ?? 'Song created successfully!';
                return redirect()->route('songcrud.index')->with('success', $message);
            }

            $data = $response->json();
            $msg = $data['message'] ?? 'Unable to create song.';
            return redirect()->back()->withInput()->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                            ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('songcrud.index')
                            ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            // Fetch the song by ID
            $responseSong = Http::withToken($token)->get("$apiBase/song/$id");
            $responseAlbums = Http::withToken($token)->get("$apiBase/albums");

            if ($responseSong->failed() || $responseAlbums->failed()) {
                return redirect()->route('songcrud.index')
                                ->with('error', 'Failed to fetch song or albums.');
            }

            $songData = $responseSong->json()['Song'] ?? null;

            if (!$songData) {
                return redirect()->route('songcrud.index')
                                ->with('error', 'Song data not found.');
            }

            // Convert song to object
            $song = (object) $songData;

            // Convert albums to objects for the select dropdown
            $albums = collect($responseAlbums->json()['albums'] ?? [])
                        ->map(fn($album) => (object) $album);

            return view('crud.song_edit', compact('song', 'albums'));

        } catch (\Exception $e) {
            return redirect()->route('songcrud.index')
                            ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'lyrics'     => 'nullable|string',
            'songwriter' => 'required|string|max:255',
            'album_id'   => 'required|integer',
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('songcrud.index')
                            ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            // Send PATCH request to API
            $response = Http::withToken($token)->patch("$apiBase/song/$id", [
                'name'       => $request->name,
                'lyrics'     => $request->lyrics,
                'songwriter' => $request->songwriter,
                'album_id'   => $request->album_id,
            ]);

            if ($response->successful() || $response->status() === 200) {
                $data = $response->json();
                $message = $data['message'] ?? "Song $id updated successfully!";
                return redirect()->route('songcrud.index')->with('success', $message);
            }

            $data = $response->json();
            $msg = $data['message'] ?? 'Unable to update the song.';
            return redirect()->route('songcrud.index')->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()->route('songcrud.index')
                            ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }



}
