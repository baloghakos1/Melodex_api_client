<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q'); // Matches name="q" in your form
        $error = null;
        $results = collect();

        if ($query) {
            try {
                $apiBase = rtrim(config('app.api_url'), '/');
                // Fetching from your API
                $response = Http::get("$apiBase/search", ['query' => $query]);
                if ($response->failed()) {
                    $error = "Could not fetch results.";
                } else {
                    // We convert the API response into a collection of objects
                    $results = collect($response->json())->flatMap(function ($items) {
                        return $items;
                    })->map(fn($item) => (object) $item);
                }
            } catch (\Exception $e) {
                $error = "Error: " . $e->getMessage();
            }
        }

        // This returns the FULL results page
        return view('search.results', compact('results', 'query', 'error'));
    }
    public function preview(Request $request)
    {
        $query = $request->input('q');

        if (!$query) return response()->json([]);

        try {
            $apiBase = rtrim(config('app.api_url'), '/');
            $response = Http::get("$apiBase/search", ['query' => $query]);

            if ($response->failed()) return response()->json([], 500);

            $data = $response->json();

            $combined = collect();
            if (isset($data['albums'])) $combined = $combined->concat($data['albums']);
            if (isset($data['artists'])) $combined = $combined->concat($data['artists']);
            if (isset($data['songs'])) $combined = $combined->concat($data['songs']);

            $randomFour = $combined->shuffle()->take(4);

            return response()->json($randomFour);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
