<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class MemberCrudController extends Controller
{
    public function index()
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');

            $responseMembers = Http::get("$apiBase/members");

            $responseArtists = Http::get("$apiBase/artists");

            if ($responseMembers->failed() || $responseArtists->failed()) {
                $members = collect();
                $artists = collect();
                $error = "Failed to fetch members or artists.";
            } else {
                $membersData = $responseMembers->json()['members'] ?? [];
                $members = collect($membersData)
                    ->map(fn($member) => (object) $member);

                $artistsData = $responseArtists->json()['artists'] ?? [];
                $artists = collect($artistsData)
                    ->map(fn($artist) => (object) $artist)
                    ->keyBy('id');

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

    public function destroy($id)
    {
        try {
            $apiBase = rtrim(config('app.api_url'), '/');
            $token = session('api_token');

            if (!$token) {
                return redirect()
                    ->route('crud.members')
                    ->with('error', 'Missing API token — authentication failed.');
            }

            $response = Http::withToken($token)->delete("$apiBase/member/$id");

            if ($response->successful() || $response->status() === 410) {
                $data = json_decode($response->body());

                $message = $data->message ?? "Member $id was successfully deleted!";

                return redirect()
                    ->route('crud.members')
                    ->with('success', $message);
            }

            $data = json_decode($response->body());
            $msg = $data->message ?? 'Unable to delete the member.';

            return redirect()
                ->route('crud.members')
                ->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()
                ->route('crud.songs')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }
}
