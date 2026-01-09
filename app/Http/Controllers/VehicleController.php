<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
// use App\Imports\EmployeeImport;
use App\Imports\VehicleImport;
use App\Models\GeoFencingPoint;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use App\Models\Plant;
use App\Models\Vehicle;
use App\Models\VehicleAttendance;
use App\Services\QrService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\HttpCache\Store;
use App\Exports\VehicleExport;
use ZipArchive;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Vehicle::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
        $vehicle = Vehicle::create($request->validated());
        return response()->json(['message' => 'Vehicle added successfully', 'data' => $vehicle], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return response()->json($vehicle);
    }

    public function showWebPage($id){
        $employee = Vehicle::findOrFail($id);
        // return $employee;

        return response()->json([
            'vehicle' => $employee,
            'qr_url' => $employee->qr_code_path
                ? asset('storage/'.$employee->qr_code_path)
                : null
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($request->validated());
        return response()->json(['message' => 'Vehicle updated successfully', 'data' => $vehicle]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return response()->json(['message' => 'Vehicle deleted successfully']);

    }

    public function webIndex(){
        return view('vehicle.index');
    }

    public function import(Request $request){
    $request->validate([
        'file' => ['required', 'file', 'mimes:xlsx,xls,csv'], 
    ]);

    try {
        // Optionally store the file (good for auditing)
        // $path = $request->file('file')->store('imports');

        Excel::import(new VehicleImport, $request->file('file'));

        return back()->with('success', 'Vehicle data imported successfully!');
        // return response()->json('success', 'Vehicle data imported successfully!');
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        // If rows fail validation inside your import class
        $failures = $e->failures();

        // return back()->withErrors([
        //     'file' => 'Some rows failed validation.',
        //     'failures' => $failures, // You can show these in your Blade
        // ]);
          return response()->withErrors([
            'file' => 'Some rows failed validation.',
            'failures' => $failures, // You can show these in your Blade
        ]);
    } catch (\Exception $e) {
        // Generic error handler (file corrupted, wrong structure, etc.)
        // return back()->withErrors([
        //     'file' => 'Import failed: ' . $e->getMessage(),
        // ]);
        return response()->json([
            'file' => 'Import failed: ' . $e->getMessage(),
        ]);
    }
    }


    public function generateQr($id){
        $employee = Vehicle::findOrFail($id);
        app(QrService::class)->generateVehicleQR($employee);

        return response()->json([
            'message' => 'QR Code Generated Successfully',
            'qr_url' => asset('storage/'.$employee->qr_code_path)
        ]);
     }

    public function downloadQr($id) {
       $employee = Vehicle::findOrFail($id);
   
       if (!$employee->qr_code_path || !Storage::disk('public')->exists($employee->qr_code_path)) {
           return response()->json([
               'status' => false,
               'message' => 'QR Code not found'
           ], 404);
       }
   
       $filePath = storage_path('app/public/' . $employee->qr_code_path);
       $downloadName = $employee->employee_code . '_qrcode.png';
   
       return response()->download($filePath, $downloadName);
    }

    public function report(Request $req){
    $query = VehicleAttendance::with(['vehicle', 'geoFencingPoint']);

    // Convert CSV string to array
    $vehicleIds = $req->vehicle_id ? explode(',', $req->vehicle_id) : [];
    $inGateIds   = $req->in_gate_id  ? explode(',', $req->in_gate_id)  : [];
    // $outGateIds  = $req->out_gate_id ? explode(',', $req->out_gate_id) : [];

    // Employee Filter
    if (!empty($employeeIds)) {
        $query->whereIn('employee_id', $vehicleIds);
    }

    // IN Gate
    if (!empty($inGateIds)) {
        $query->whereIn('geo_fencing_point_id', $inGateIds);
    }

    // OUT Gate
    if (!empty($outGateIds)) {
        // $query->whereIn('out_geo_fencing_point_id', $outGateIds);
    }

    // Date Filters
    if ($req->date_type === 'today') {
        $query->whereDate('attendance_date', Carbon::today());
    }
    if ($req->date_type === 'tomorrow') {
        $query->whereDate('attendance_date', Carbon::tomorrow());
    }
    if ($req->date_type === 'weekly') {
        $query->whereBetween('attendance_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }
    if ($req->date_type === 'monthly') {
        $query->whereMonth('attendance_date', Carbon::now()->month);
    }
    if ($req->date_type === 'yearly') {
        $query->whereYear('attendance_date', Carbon::now()->year);
    }
    if ($req->date_type === 'custom') {
        $query->whereBetween('attendance_date', [$req->from_date, $req->to_date]);
    }

    return response()->json(
        $query->orderBy('id', 'DESC')->get()
    );
}

    public function reportIndex(){
        $employees = Vehicle::orderByDesc('name')->get();
        $geoFencingPoints = GeoFencingPoint::orderByDesc('location')->get();
        return view('vehicle.report',[
            'employees'=>$employees,
            'geoFencingPoints' => $geoFencingPoints,
            'plants' => Plant::orderByDesc('name')->get()
        ]);
    }
    
   public function downloadTemplate(){
      return Excel::download(new VehicleExport, 'vehicle_import_template.xlsx');
   }
   
   public function downloadAllQRCodes(){
    // 1️⃣ Saare vehicles fetch karo jinke QR codes stored hain
    $vehicles = Vehicle::whereNotNull('qr_code_path')->get();
    // return $vehicles;

    if ($vehicles->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No QR Codes found for vehicles.'
        ], 404);
    }

    // 2️⃣ Temporary ZIP file path
    $zipFileName = 'vehicle_qr_codes_' . time() . '.zip';
    $zipPath = storage_path('app/public/' . $zipFileName);

    $zip = new ZipArchive;

    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {

        foreach ($vehicles as $vehicle) {
            // QR file exist check
            if (!Storage::disk('public')->exists($vehicle->qr_code_path)) {
                continue;
            }

            // File content get karo
            $fileContent = Storage::disk('public')->get($vehicle->qr_code_path);

            // ZIP me filename set karo (custom, readable)
            $fileName = 'QR_' . ($vehicle->name ?? 'vehicle') . '_' . $vehicle->vehicle_no . '.png' ;

            $zip->addFromString($fileName, $fileContent);
        }

        $zip->close();
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Failed to create ZIP file.'
        ], 500);
    }

   
       return response()->download($zipPath)->deleteFileAfterSend(true);
   }
   
      public function downloadAllQRCodesPDF(){
    // 1️⃣ Vehicles fetch करो जिनके QR codes saved हैं
    $vehicles = Vehicle::whereNotNull('qr_code_path')->get();

    if ($vehicles->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No QR Codes found.'
        ], 404);
    }

    // 2️⃣ QR data view को pass करने के लिए array ready करो
    $qrData = [];

    foreach ($vehicles as $vehicle) {

        if (!Storage::disk('public')->exists($vehicle->qr_code_path)) {
            continue;
        }

        $qrData[] = [
            'employee_no' => $vehicle->emp_code,
            'pass_no'     =>  $passno='ASVP' . sprintf('%04d', $vehicle->pass_no),   // Pass number yahi hota hai
            'qr_image'    => base64_encode(Storage::disk('public')->get($vehicle->qr_code_path))
        ];
    }

    // 3️⃣ PDF Generate करो
    $pdf = FacadePdf::loadView('pdfs.vehicle_all_qr_code', compact('qrData'));

    return $pdf->download('vehicle_qr_codes.pdf');
 }
   
}
