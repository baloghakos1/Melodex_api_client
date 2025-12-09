<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class MemberCrudController extends Controller
{
    public function index()
{
    try {
        $apiBase = rtrim(config('app.api_url'), '/');

        // 1. Fetch all members
        $responseMembers = Http::get("$apiBase/members");

        // 2. Fetch all artists (if needed for relationships)
        $responseArtists = Http::get("$apiBase/artists");

        if ($responseMembers->failed() || $responseArtists->failed()) {
            $members = collect();
            $artists = collect();
            $error = "Failed to fetch members or artists.";
        } else {
            // Members data
            $membersData = $responseMembers->json()['members'] ?? [];
            $members = collect($membersData)
                ->map(fn($member) => (object) $member);

            // Artists data
            $artistsData = $responseArtists->json()['artists'] ?? [];
            $artists = collect($artistsData)
                ->map(fn($artist) => (object) $artist)
                ->keyBy('id'); // Key by ID for easy lookup

            // Attach artist object to each member (if member has artist_id)
            $members = $members->map(function ($member) use ($artists) {
                $member->artist = $artists[$member->artist_id] ?? null;
                return $member;
            });

            $error = null;
        }

    } catch (\Exception $e) {
        $members = collect();
        $artists = collect();
        $error = "Error fetching members or artists: " . $e->getMessage();
    }

    return view('crud.members', compact('members', 'artists', 'error'));
}
}
