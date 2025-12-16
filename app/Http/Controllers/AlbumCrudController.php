<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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

    public function create()
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('albumcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            // Fetch artists for the dropdown
            $responseArtists = Http::withToken($token)->get("$apiBase/artists");

            if ($responseArtists->failed()) {
                $artists = collect();
                $error = 'Failed to fetch artists.';
            } else {
                $artistsData = $responseArtists->json()['artists'] ?? [];
                $artists = collect($artistsData)->map(fn ($artist) => (object) $artist);
                $error = null;
            }

            return view('crud.album_create', compact('artists', 'error'));

        } catch (\Exception $e) {
            return redirect()->route('albumcrud.index')
                ->with('error', 'Error fetching artists: ' . $e->getMessage());
        }
    }

    /**
     * Store a new album via API
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'cover'     => 'nullable|string',
            'year'      => 'required|integer',
            'genre'     => 'required|string|max:255',
            'artist_id' => 'required|exists:artists,id',
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('albumcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)->post("$apiBase/album", $validated);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['message'] ?? 'Album created successfully!';
                return redirect()->route('albumcrud.index')->with('success', $message);
            }

            $msg = $response->json()['message'] ?? 'Unable to create album.';
            return redirect()->back()->withInput()->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

    /**
     * Show the form to edit an existing album
     */
    public function edit($id)
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('albumcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            // Fetch album
            $response = Http::withToken($token)->get("$apiBase/album/$id");

            if ($response->failed()) {
                return redirect()->route('albumcrud.index')
                    ->with('error', 'Failed to fetch album.');
            }

            $albumData = $response->json()['Album'] ?? null;

            if (!$albumData) {
                return redirect()->route('albumcrud.index')
                    ->with('error', 'Album data not found.');
            }

            $album = (object) $albumData;

            // Fetch artists for dropdown
            $artistsResponse = Http::withToken($token)->get("$apiBase/artists");
            $artistsData = $artistsResponse->json()['artists'] ?? [];
            $artists = collect($artistsData)->map(fn ($a) => (object) $a);

            return view('crud.album_edit', compact('album', 'artists'));

        } catch (\Exception $e) {
            return redirect()->route('albumcrud.index')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing album via API
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'cover'     => 'nullable|string',
            'year'      => 'required|integer',
            'genre'     => 'required|string|max:255',
            'artist_id' => 'required|exists:artists,id',
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('albumcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)
                ->patch("$apiBase/album/$id", [
                    'name'      => $request->name,
                    'cover'     => $request->cover,
                    'year'      => $request->year,
                    'genre'     => $request->genre,
                    'artist_id' => $request->artist_id,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['message'] ?? "Album $id updated successfully!";
                return redirect()->route('albumcrud.index')->with('success', $message);
            }

            $msg = $response->json()['message'] ?? 'Unable to update album.';
            return redirect()->route('albumcrud.index')->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()->route('albumcrud.index')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

}
