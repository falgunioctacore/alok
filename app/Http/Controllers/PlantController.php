<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlantRequest;
use App\Models\Plant;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plants = Plant::with('geoFencingPoints')->get();
        return response()->json($plants);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlantRequest $request)
    {
        $plant = Plant::create($request->validated());
        return response()->json(['message' => 'Plant created successfully', 'data' => $plant], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   $plant = Plant::with('geoFencingPoints')->findOrFail($id);
        return response()->json($plant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlantRequest $request, string $id)
    {
        $plant = Plant::findOrFail($id);
        $plant->update($request->validated());
        return response()->json(['message' => 'Plant updated successfully', 'data' => $plant]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plant = Plant::findOrFail($id);
        $plant->delete();
        return response()->json(['message' => 'Plant deleted successfully']);

    }
}
