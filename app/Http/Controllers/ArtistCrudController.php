<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

class ArtistCrudController extends Controller
{
    public function index() {
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

        return view('crud.artists', compact('artists', 'error'));
    }
}
