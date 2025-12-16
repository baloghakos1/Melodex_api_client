<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf; // if using barryvdh/laravel-dompdf
use Maatwebsite\Excel\Facades\Excel; // if using Laravel Excel
use App\Exports\ArtistsExport; // For Laravel Excel export (create this)

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

    public function create()
    {
        $token = session('api_token');
        if (!$token) {
            return redirect()->route('artistcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        return view('crud.artist_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'image' => 'nullable|string',
            'description' => 'required|string',
            'is_band' => 'required|string'
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('artistcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)->post("$apiBase/artist", $validated);

            if ($response->successful()) {
                $message = $response->json()['message'] ?? 'Artist created successfully!';
                return redirect()->route('artistcrud.index')->with('success', $message);
            }

            $msg = $response->json()['message'] ?? 'Unable to create artist.';
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
            return redirect()->route('artistcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)->get("$apiBase/artist/$id");

            if ($response->failed()) {
                return redirect()->route('artistcrud.index')
                    ->with('error', 'Failed to fetch artist.');
            }

            $artistData = $response->json()['artist'] ?? null;

            if (!$artistData) {
                return redirect()->route('artistcrud.index')
                    ->with('error', 'Artist data not found.');
            }

            $artist = (object) $artistData;

            return view('crud.artist_edit', compact('artist'));

        } catch (\Exception $e) {
            return redirect()->route('artistcrud.index')
                ->with('error', 'Failed to communicate with the API: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'image' => 'nullable|string',
            'description' => 'required|string',
            'is_band' => 'required|string'
        ]);

        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('artistcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            $response = Http::withToken($token)->patch("$apiBase/artist/$id", $validated);

            if ($response->successful()) {
                $message = $response->json()['message'] ?? "Artist $id updated successfully!";
                return redirect()->route('artistcrud.index')->with('success', $message);
            }

            $msg = $response->json()['message'] ?? 'Unable to update artist.';
            return redirect()->back()->withInput()->with('error', "API Error: $msg");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
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
            $response = Http::withToken($token)->get("$apiBase/artists");

            if ($response->failed()) {
                return redirect()->back()->with('error', 'Failed to fetch artists from API.');
            }

            $artistsData = $response->json()['artists'] ?? [];

            $csvData = "ID,Name,Nationality,Image,Description,Is Band\n";

            foreach ($artistsData as $artist) {
                $csvData .= implode(',', [
                    $artist['id'],
                    $artist['name'],
                    $artist['nationality'],
                    $artist['image'] ?? '',
                    str_replace(',', ';', $artist['description']), // avoid breaking CSV
                    $artist['is_band'] ? 'Yes' : 'No'
                ]) . "\n";
            }

            $filename = "artists_" . date('Y-m-d_H-i-s') . ".csv";

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"$filename\"");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to fetch artists: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $apiBase = rtrim(config('app.api_url'), '/');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('artistcrud.index')
                ->with('error', 'Missing API token — authentication failed.');
        }

        try {
            // Fetch artists from API
            $response = Http::withToken($token)->get("$apiBase/artists");

            if ($response->failed()) {
                return redirect()->route('artistcrud.index')
                    ->with('error', 'Failed to fetch artists.');
            }

            // Get artists data from the response
            $artistsData = $response->json()['artists'] ?? []; // Access 'artists' from the response

            // Pass data to the PDF view
            $pdf = Pdf::loadView('crud.artist_pdf', ['artists' => $artistsData]);

            return $pdf->download('artists_' . date('Y-m-d_H-i-s') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('artistcrud.index')
                ->with('error', 'Failed to fetch artists: ' . $e->getMessage());
        }
    }

}


