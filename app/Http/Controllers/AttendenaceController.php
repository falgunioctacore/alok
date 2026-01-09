<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AccessPoint;
use App\Models\Employee;
use App\Models\GeoFencingPoint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendenaceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with(['employee', 'inGeoFencingPoint', 'outGeoFencingPoint'])
            ->orderByDesc('id')->get();
        return response()->json($attendances);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:empolyees,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:in,out',
            'reason'=>'nullable'
        ]);

        $employee = Employee::with('geoFencingPoints')->find($request->employee_id);
        $type = $request->type;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $nearestGate = null;
        foreach ($employee->geoFencingPoints as $gate) {
            $distance = $this->haversineDistance($latitude, $longitude, $gate->latitude, $gate->longitude);
            if ($distance <= ($gate->radius ?? 20)) {
                $nearestGate = $gate;
                break;
            }
        }
        if (!$nearestGate) {
            return response()->json(['status' => 'error', 'message' => 'You are not near any permitted gate.'], 403);
        }

        if ($request->type === 'in') {
            // ✅ Check if already IN
            $openAttendance = Attendance::where('employee_id', $employee->id)
                ->whereNull('out_time')
                ->first();

            if ($openAttendance) {
                return response()->json(['status' => 'error', 'message' => 'You already marked IN. Please mark OUT first.'], 400);
            }

            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'geo_fencing_point_id' => $nearestGate->id,
                'in_time' => Carbon::now(),
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Marked IN successfully', 'data' => $attendance]);
        }

        if ($request->type === 'out') {
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereNull('out_time')
                ->first();

            if (!$attendance) {
                return response()->json(['status' => 'error', 'message' => 'You are not currently marked IN.'], 400);
            }

            $attendance->update([
                'out_time' => Carbon::now(),
                'reason' => $request->reason,
                'geo_fencing_point_id' => $nearestGate->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Marked OUT successfully', 'data' => $attendance]);
        }


        


    }

    private function findNearestGate($lat, $lng)
    {
        $points = GeoFencingPoint::all();
        $nearest = null;
        $minDistance = 99999;

        foreach ($points as $point) {
            $distance = $this->haversineDistance($lat, $lng, $point->latitude, $point->longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $point;
            }
        }

        return $minDistance <= 20 ? $nearest : null; // within 20 meters
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function show($id)
    {
        return response()->json(Attendance::with(['employee', 'inAccessPoint', 'outAccessPoint'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->all());
        return response()->json(['message' => 'Attendance updated successfully', 'data' => $attendance]);
    }

    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function webIndex(){
        return view('attendence.index');
    }


    public function markPage(){
        return view('employees.mobile-attendence2');
    }

// public function mark(Request $request)
// {
//     $validated = $request->validate([
//         'latitude' => 'required|numeric',
//         'longitude' => 'required|numeric',
//         'reason' => 'nullable|string',
//     ]);

  
//     $employee = Employee::where('emp_code', $request->emp_code)->first();

//     if (!$employee) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Employee not found.',
//         ], 404);
//     }

//     // 🔹 Find nearest access point (within 20m radius)
//     $accessPoint = GeoFencingPoint::all()
//         ->map(function ($point) use ($validated) {
//             $point->distance = $this->distance(
//                 $validated['latitude'], $validated['longitude'],
//                 $point->latitude, $point->longitude
//             );
//             return $point;
//         })
//         ->sortBy('distance')
//         ->first();

//     if (!$accessPoint || $accessPoint->distance > 0.02) { // 0.02 km = 20m
//         return response()->json([
//             'status' => 'error',
//             'message' => 'You are not within the allowed area (20m radius).',
//         ], 403);
//     }

//     // 🔹 Check if employee is permitted at this access point
//     $isPermitted = $employee->geoFencingPoints()->where('geo_fencing_points.id', $accessPoint->id)->exists();

//     if (!$isPermitted) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'You are not permitted to access this gate.',
//         ], 403);
//     }

//     // 🔹 Get today’s attendance record
//     $attendance = Attendance::where('employee_id', $employee->id)
//         ->whereDate('created_at', today())
//         ->latest()
//         ->first();

//     // 🔹 MARK IN (if no active IN or OUT completed)
//     if (!$attendance || $attendance->out_time) {
//         $attendance = Attendance::create([
//             'employee_id' => $employee->id,
//             'in_geo_fencing_point_id' => $accessPoint->id,
//             'in_latitude' => $validated['latitude'],
//             'in_longitude' => $validated['longitude'],
//             'in_time' => now(),
//         ]);

//         return response()->json([
//             'status' => 'success',
//             'message' => "Marked IN successfully at {$accessPoint->location}.",
//             'data' => $attendance,
//         ]);
//     }

//     // 🔹 Check if already OUT or not yet IN
//     if (!$attendance->in_time) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'You must mark IN before marking OUT.',
//         ], 422);
//     }

//     if ($attendance->out_time) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'You are already marked OUT for today.',
//         ], 422);
//     }

//     // 🔹 MARK OUT
//     $attendance->update([
//         'out_geo_fencing_point_id' => $accessPoint->id,
//         'out_latitude' => $validated['latitude'],
//         'out_longitude' => $validated['longitude'],
//         'out_time' => now(),
//         'reason' => $validated['reason'],
//     ]);

//     return response()->json([
//         'status' => 'success',
//         'message' => "Marked OUT successfully at {$accessPoint->location}.",
//         'data' => $attendance,
//     ]);
// }


    private function distance($lat1, $lon1, $lat2, $lon2){
        $R = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLon/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    public function employeeIndex(Request $request){
        //   $user = $request->user();
        // $employee = Employee::where('emp_code',$request->emp_code)->first();
         $employee = Employee::where('emp_code',$request->emp_code)->first();

        
        if(empty($employee)){
            return response()->json([
                'message'=>'Not Found'
            ],404);
         }

        // Get current attendance if exists
        // $todayAttendance = Attendance::with(['employee', 'inGeoFencingPoint', 'outGeoFencingPoint'])->where('employee_id', $employee->id)
        //     ->whereDate('created_at', Carbon::today())
        //     ->latest()
        //     ->first();

        $todayAttendance = Attendance::with(['employee', 'geoFencingPoint'])->where('employee_id', $employee->id)
            //->whereDate('created_at', Carbon::today())
            ->latest()
            ->first();

        // if($todayAttendance){
        //     return response()->json([
        //         'message'=>'Not Found'
        //     ],404);
        //  }

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'emp_name' => $employee->emp_name,
                'emp_code'=> $employee->emp_code,
            ],
            'attendance' => [
                  'last_status' => $todayAttendance->type??'N.A'
                ],
            // 'month_records' => $monthRecords,
        ]);
    }

    public function monthRecords(Request $request){
        //   $user = $request->user();
        // return $request->all();
        $employee = Employee::where('emp_code',$request->emp_code)->first(); 
        //  $employee = Employee::where('employee_id',$request->emp_code)->first();
          if(!$employee){
            return "";
          }
        $monthRecords = Attendance::with(['employee', 'geoFencingPoint'])
            ->where('employee_id', $employee->id)
            // ->where('emp_code', $employee->emp_code)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy('created_at', 'desc')
            ->get()->map(function($record){
              return[
                'type'=>$record->type,
                'attendance_date_time' => $record->attendance_date,
                'geo_fencing_point'=>$record->geoFencingPoint->location??'-',
                'reason'=>$record->reason,
              ];
            });
            
            return response()->json([
            'employee' => [
                'id' => $employee->id,
                'emp_name' => $employee->emp_name,
            ],
         'month_records' => $monthRecords,
        ]);
    }
    

    public function mark(Request $request)
    {
        $validated = $request->validate([
            'emp_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'nullable|in:in,out',
            'reason' => 'nullable|string',
        ]);

     if (!isset($validated['type']) || !$validated['type']) {
         $validated['type'] = 'in';
     } 


        // $employee = Employee::where('employee_id', $request->emp_code)->first();
        $employee = Employee::where('emp_code', $request->emp_code)->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.',
            ], 404);
        }

        // 🔹 Find nearest access point (within 20m radius)
        // $accessPoint = GeoFencingPoint::all()
        //     ->map(function ($point) use ($validated) {
        //         $point->distance = $this->distance(
        //             $validated['latitude'],
        //             $validated['longitude'],
        //             $point->latitude,
        //             $point->longitude
        //         );
        //         return $point;
        //     })
        //     ->sortBy('distance')
        //     ->first();
        
    $accessPoint = GeoFencingPoint::get()
    ->map(function ($point) use ($validated) {

        $point->distance = $this->distance(
            (float)$validated['latitude'],
            (float)$validated['longitude'],
            (float)$point->latitude,
            (float)$point->longitude
        );

        return $point;
    })
    ->sortBy('distance')->first();      

     if (!$accessPoint) { 
        return response()->json([
            'status' => 'error',
            'message' => 'You are not within the allowed area (20m). Distance: ' 
                         . round($accessPoint->distance * 1000, 2) . ' meters. '
                         . 'Nearest Gate: ' . $accessPoint->location,
        ], 403);
    }


        // 🔹 Check if employee is permitted at this access point
        // $isPermitted = $employee->plant->geoFencingPoints()->where('geo_fencing_points.id', $accessPoint->id)->exists();

        // if (!$isPermitted) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'You are not permitted to access this gate.',
        //     ], 403);
        // }

        // 🔹 Create attendance record directly
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'type' => $validated['type'],
            'attendance_date' => now(),
            'geo_fencing_point_id' => $accessPoint->id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'reason' => $validated['type'] === 'out' ? $validated['reason'] : null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Marked " . strtoupper($validated['type']) . " successfully at {$accessPoint->location}.",
            'data' => $attendance,
        ]);
    }

}



