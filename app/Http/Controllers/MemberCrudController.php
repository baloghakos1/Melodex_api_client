<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function create()
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('membercrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $responseArtists = Http::withToken($token)->get("$apiBase/artists");

            if ($responseArtists->failed()) {
                $artists = collect();
                $error = 'Failed to fetch artists.';
            } else {
                $artistsData = $responseArtists->json()['artists'] ?? [];
                $artists = collect($artistsData)->map(fn ($artist) => (object) $artist);
                $error = null;
            }

            return view('crud.member_create', compact('artists', 'error'));

        } catch (\Exception $e) {
            return redirect()->route('membercrud.index')
                ->with('error', 'Error fetching artists: ' . $e->getMessage());
        }
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'instrument' => 'required|string|max:255',
            'year'       => 'required|integer',
            'artist_id'  => 'required|exists:artists,id',
            'image'      => 'nullable|string',
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('membercrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)
                ->post("$apiBase/member", $validated);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['message'] ?? 'Member created successfully!';
                return redirect()->route('membercrud.index')->with('success', $message);
            }

            $msg = $response->json()['message'] ?? 'Unable to create member.';
            return redirect()->back()->withInput()
                ->with('error', "API Error: $msg");

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
            return redirect()->route('membercrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            // Fetch member
            $response = Http::withToken($token)->get("$apiBase/member/$id");

            if ($response->failed()) {
                return redirect()->route('membercrud.index')
                    ->with('error', 'Failed to fetch member.');
            }

            $memberData = $response->json()['Member'] ?? null;

            if (!$memberData) {
                return redirect()->route('membercrud.index')
                    ->with('error', 'Member data not found.');
            }

            $member = (object) $memberData;

            $artistsResponse = Http::withToken($token)->get("$apiBase/artists");

            $artistsData = $artistsResponse->json()['artists'] ?? [];

            $artists = collect($artistsData)->map(fn($a) => (object) $a);

            return view('crud.member_edit', compact('member', 'artists'));

        } catch (\Exception $e) {
            return redirect()->route('membercrud.index')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'instrument' => 'required|string|max:255',
            'year'       => 'required|integer',
            'artist_id'  => 'required|exists:artists,id',
            'image'      => 'nullable|string',
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('membercrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)
                ->patch("$apiBase/member/$id", [
                    'name'       => $request->name,
                    'instrument' => $request->instrument,
                    'year'       => $request->year,
                    'artist_id'  => $request->artist_id,
                    'image'      => $request->image,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['message'] ?? "Member $id updated successfully!";
                return redirect()->route('membercrud.index')->with('success', $message);
            }

            $msg = $response->json()['message'] ?? 'Unable to update member.';
            return redirect()->route('membercrud.index')
                ->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()->route('membercrud.index')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->back()->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $responseMembers = Http::withToken($token)->get("$apiBase/members");
            $responseArtists = Http::withToken($token)->get("$apiBase/artists");

            if ($responseMembers->failed() || $responseArtists->failed()) {
                return redirect()->back()->with('error', 'Failed to fetch members or artists from API.');
            }

            $membersData = $responseMembers->json()['members'] ?? [];
            $artistsData = $responseArtists->json()['artists'] ?? [];

            $artists = collect($artistsData)->keyBy('id');

            $filename = "members_" . date('Y-m-d_H-i-s') . ".csv";

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($membersData, $artists) {
                $output = fopen('php://output', 'w');
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'ID',
                    'Name',
                    'Instrument',
                    'Year',
                    'Artist',
                    'Image'
                ], ';');

                foreach ($membersData as $member) {
                    $artistName = $artists[$member['artist_id']]['name'] ?? 'N/A';

                    fputcsv($output, [
                        $member['id'],
                        $member['name'],
                        $member['instrument'],
                        $member['year'],
                        $artistName,
                        $member['image'] ?? '',
                    ], ';');
                }

                fclose($output);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to fetch members: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('membercrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            // Fetch members and artists
            $responseMembers = Http::withToken($token)->get("$apiBase/members");
            $responseArtists = Http::withToken($token)->get("$apiBase/artists");

            if ($responseMembers->failed() || $responseArtists->failed()) {
                return redirect()->route('membercrud.index')
                    ->with('error', 'Failed to fetch members or artists.');
            }

            $membersData = $responseMembers->json()['members'] ?? [];
            $artistsData = $responseArtists->json()['artists'] ?? [];

            // Key artists by ID for easy lookup
            $artists = collect($artistsData)->keyBy('id');

            // Add artist names to members
            $members = collect($membersData)->map(function ($member) use ($artists) {
                return (object) array_merge($member, [
                    'artist_name' => $artists[$member['artist_id']]['name'] ?? 'N/A'
                ]);
            });

            // Generate PDF
            $pdf = Pdf::loadView('crud.member_pdf', compact('members'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'chroot' => public_path(),
                ]);

            return $pdf->download('members_' . date('Y-m-d_H-i-s') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('membercrud.index')
                ->with('error', 'Failed to fetch members: ' . $e->getMessage());
        }
    }
}
