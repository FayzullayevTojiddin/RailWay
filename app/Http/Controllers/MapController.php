<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $stations = Station::all()->map(function ($station) {
            return [
                'id' => $station->id,
                'title' => $station->title,
                'type' => $station->type,
                'coordinates' => $station->coordinates,
                'description' => $station->description,
                'images' => $station->images ?? [],
                'details' => $station->details ?? []
            ];
        });

        dd($stations);

        return view('map.index', compact('stations'));
    }

    public function getStations()
    {
        $stations = Station::all()->map(function ($station) {
            return [
                'id' => $station->id,
                'title' => $station->title,
                'type' => $station->type,
                'coordinates' => $station->coordinates,
                'description' => $station->description,
                'images' => $station->images ?? [],
                'details' => $station->details ?? []
            ];
        });

        return response()->json($stations);
    }

    public function show($id)
    {
        $station = Station::findOrFail($id);
        
        return response()->json([
            'id' => $station->id,
            'title' => $station->title,
            'type' => $station->type,
            'coordinates' => $station->coordinates,
            'description' => $station->description,
            'images' => $station->images ?? [],
            'details' => $station->details ?? [],
            'employees_count' => $station->employees()->count(),
            'branch_railways_count' => $station->branchRailways()->count(),
            'cadastres_count' => $station->cadastres()->count()
        ]);
    }
}