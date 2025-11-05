<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Xarita sahifasini ko'rsatish
     */
    public function index()
    {
        // Barcha stansiyalarni olish
        $stations = Station::all()->map(function ($station) {
            return [
                'id' => $station->id,
                'title' => $station->title,
                'type' => $station->type,
                'coordinates' => $station->coordinates, // {x: 30, y: 70, latitude: 37.2242, longitude: 67.2783}
                'description' => $station->description,
                'images' => $station->images ?? [],
                'details' => $station->details ?? []
            ];
        });

        return view('map.index', compact('stations'));
    }

    /**
     * API - Stansiyalar ro'yxatini qaytarish
     */
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

    /**
     * Bitta stansiya ma'lumotlarini olish
     */
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
            'main_railways_count' => $station->mainRailways()->count(),
            'cadastres_count' => $station->cadastres()->count()
        ]);
    }
}