<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\GeoFencingPoint;
use App\Models\Plant;
use App\Models\SiteArea;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TrashController extends Controller
{
   public function index()
    {
         $models = [
            'Plant' => Plant::onlyTrashed()->get(),
            'Department' => Department::onlyTrashed()->get(),
            'SiteArea' => SiteArea::onlyTrashed()->get(),
            'Employee' => Employee::onlyTrashed()->get(),
            'Vehicle' => Vehicle::onlyTrashed()->get(),
            'GeoFencingPoint' => GeoFencingPoint::onlyTrashed()->get(),
        ];

        return view('trash.index', compact('models'));
    }

        public function restore($model, $id)
    {
        $model = "\\App\\Models\\$model";
        $model::withTrashed()->find($id)->restore();

        // return back()->with('success', "$model restored!");
        return response()->json(['success' => "$model restored successfully"]);
    }

    public function forceDelete($model, $id)
    {
        $model = "\\App\\Models\\$model";
        $model::withTrashed()->find($id)->forceDelete();

        // return back()->with('success', "$model deleted permanently!");
        return response()->json(['success' => "$model deleted permanently"]);
    }
}
