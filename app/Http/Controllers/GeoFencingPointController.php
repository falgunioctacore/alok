<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeoFencingPoinRequest;
use App\Models\GeoFencingPoint;
use Illuminate\Http\Request;

class GeoFencingPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $points = GeoFencingPoint::with('plant')
                  ->get()
                  ->map(function($point){
                    return[
                    'id' => $point->id,
                    'plant' => $point->plant?->name,
                    'gate'=>$point->gate_count??'',
                    'location' => $point->location,
                    'latitude' => $point->latitude,
                    'longitude' => $point->longitude,
                    ];
                  });
        return response()->json($points);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GeoFencingPoinRequest $request)
    {
        $point = GeoFencingPoint::create($request->validated());
        return response()->json(['message' => 'Access Control Point created successfully', 'data' => $point], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $point = GeoFencingPoint::with('plant')->findOrFail($id);
        return response()->json($point);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GeoFencingPoinRequest $request, string $id)
    {
        $point = GeoFencingPoint::findOrFail($id);
        $point->update($request->validated());
        return response()->json(['message' => 'Access Control Point updated successfully', 'data' => $point]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $point = GeoFencingPoint::findOrFail($id);
        $point->delete();
        return response()->json(['message' => 'Access Control Point deleted successfully']);
    }

    public function webIndex(){
    //   $points = GeoFencingPoint::with('plant')->get();
      return view('access_points.index',);   
    }
}
