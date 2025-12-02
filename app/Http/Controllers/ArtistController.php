<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ArtistController extends Controller
{
    public function index()
    {
        try {
            $apiUrl = rtrim(config('app.api_url'), '/') . '/artists';
            $response = Http::get($apiUrl);

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

}
