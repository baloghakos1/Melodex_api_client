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

    public function destroy($id)
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');
            $token = session('api_token');

            if (!$token) {
                return redirect()
                    ->route('crud.artists')
                    ->with('error', 'Missing API token — authentication failed.');
            }

            $response = Http::withToken($token)->delete("$apiBase/artist/$id");

            if ($response->successful() || $response->status() === 410) {
                $data = json_decode($response->body());

                $message = $data->message ?? "Artist $id was successfully deleted!";

                return redirect()
                    ->route('crud.artists')
                    ->with('success', $message);
            }

            $data = json_decode($response->body());
            $msg = $data->message ?? 'Unable to delete the artist.';

            return redirect()
                ->route('crud.artists')
                ->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()
                ->route('crud.songs')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }
}
