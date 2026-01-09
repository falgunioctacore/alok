<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiteAreaRequest;
use App\Models\Department;
use App\Models\SiteArea;
use Illuminate\Http\Request;

class SiteAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(SiteArea::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SiteAreaRequest $request)
    {
        $siteArea = SiteArea::create($request->validated());
        return response()->json(['message' => 'Site Area is added successfully', 'data' => $siteArea], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $siteArea = SiteArea::findOrFail($id);
        return response()->json($siteArea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SiteAreaRequest $request, string $id)
    {
        $siteArea = SiteArea::findOrFail($id);
        $siteArea->update($request->validated());
        return response()->json(['message' => 'Site Area is updated successfully', 'data' => $siteArea]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $siteArea = SiteArea::findOrFail($id);
        $siteArea->delete();
        return response()->json(['message' => 'Site Area deleted successfully']);
    }

    public function webIndex(){
        return view('site_areas.index');
    }
}
