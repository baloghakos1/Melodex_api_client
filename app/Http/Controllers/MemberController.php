<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class MemberController extends Controller
{
    public function index(string $artist_id)
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            // Fetch artist info
            $responseArtist = Http::get("$apiBase/artist/$artist_id");
            $artistData = $responseArtist->json()['artist'] ?? null;

            if (!$artistData) {
                $artist = (object)[
                    'id' => $artist_id,
                    'name' => 'Unknown Artist',
                    'image' => asset('image/default_artist.png'),
                    'description' => '',
                    'nationality' => null,
                    'is_band' => 'no',
                ];
                $members = collect([$artist]);
                $error = "Failed to fetch artist data.";
            } else {
                $artist = (object)[
                    'id' => $artistData['id'] ?? $artist_id,
                    'name' => $artistData['name'] ?? 'Unknown Artist',
                    'image' => $artistData['image'] ?? asset('image/default_artist.png'),
                    'description' => $artistData['description'] ?? '',
                    'nationality' => $artistData['nationality'] ?? null,
                    'is_band' => $artistData['is_band'] ?? 'no',
                ];

                // Fetch members only if band
                if ($artist->is_band === 'yes') {
                    $responseMembers = Http::get("$apiBase/artist/$artist_id/members");
                    $membersData = $responseMembers->json();
                    $members = collect($membersData['members'] ?? [])
                        ->map(fn($member) => (object)$member);
                } else {
                    // solo artist = just themselves
                    $members = collect([$artist]);
                }

                $error = null;
            }
        } catch (\Exception $e) {
            $artist = (object)[
                'id' => $artist_id,
                'name' => 'Unknown Artist',
                'image' => asset('image/default_artist.png'),
                'description' => '',
                'nationality' => null,
                'is_band' => 'no',
            ];
            $members = collect([$artist]);
            $error = "Error fetching artist: " . $e->getMessage();
        }

        return view('artists.description', compact('artist', 'members', 'error'));
    }
}
