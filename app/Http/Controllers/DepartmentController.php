<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DepartmentImport;
use App\Exports\DepartmentExport;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Department::all()); //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentRequest $request)
    {
        $departMent = Department::create($request->validated());
        return response()->json(['message' => 'Department  is added successfully', 'data' => $departMent], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $departMent = Department::findOrFail($id);
        return response()->json($departMent);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentRequest $request, string $id)
    {
        $departMent = Department::findOrFail($id);
        $departMent->update($request->validated());
        return response()->json(['message' => 'Department is updated successfully', 'data' => $departMent]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $departMent = Department::findOrFail($id);
        $departMent->delete();
        return response()->json(['message' => 'Department deleted successfully']);
    }

    public function webIndex()
    {
        return view('departments.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt'
        ]);

        Excel::import(new DepartmentImport(), $request->file('file'));
        return back()->with('success', 'Department data imported successfully!');
    }

    public function downloadExport()
    {
        return Excel::download(new DepartmentExport(), 'departments_export.xlsx');
    }
}
