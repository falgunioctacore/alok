<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;

use App\Exports\EmployeeExport;
use App\Imports\EmployeeImport;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Empolyee;
use App\Models\GeoFencingPoint;
use App\Models\Plant;
use App\Services\QrService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $employees = Employee::with([
    'siteArea:id,name',
    'department:id,name',
    'plant:id,name'
])->get()->map(function ($emp) {
    return [
        'id'             => $emp->id,
        'emp_name'       => $emp->emp_name,
        'emp_code'       => $emp->emp_code,
        'employee_id'    => $emp->employee_id,
        'emp_email_id'   => $emp->emp_email_id,
        'site_area_id'   => $emp->site_area_id,
        'plant_id'       => $emp->plant_id,
        'department_id'  => $emp->department_id,
        'emp_mobile_no'  => $emp->emp_mobile_no,
        'deleted_at'     => $emp->deleted_at,
        'created_at'     => $emp->created_at,
        'updated_at'     => $emp->updated_at,
        'site_area'      => $emp->siteArea?->name,
        'department'     => $emp->department?->name,
        'plant'          => $emp->plant?->name,
    ];
   });

        
        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
         $employee = Employee::create($request->validated());
        return response()->json(['message' => 'Employee added successfully', 'data' => $employee], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::findOrFail($id);
        return response()->json($employee);
    }
     public function showWebPage($id){
        $employee = Employee::with('plant')->findOrFail($id);

        return response()->json([
            'employee' => $employee,
            'qr_url' => $employee->qr_code_path
                ? asset('storage/'.$employee->qr_code_path)
                : null
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, string $id)
    {
         $employee = Employee::findOrFail($id);
        $employee->update($request->validated());
        return response()->json(['message' => 'Employee updated successfully', 'data' => $employee]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }

    public function webIndex(){
        
        return view('employees.index');
    }

       public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new EmployeeImport(), $request->file('file'));

        return back()->with('success', 'Employee data imported successfully!');
    }

public function report(Request $req)
{
    $query = Attendance::with(['employee', 'geoFencingPoint']);

    // Convert CSV string to array
    $employeeIds = $req->employee_id ? explode(',', $req->employee_id) : [];
    $plantIds    = $req->plant_id    ? explode(',', $req->plant_id)    : [];
    // $inGateIds   = $req->in_gate_id  ? explode(',', $req->in_gate_id)  : [];
    // $outGateIds  = $req->out_gate_id ? explode(',', $req->out_gate_id) : [];
    $gateIds   = $req->gate_id  ? explode(',', $req->gate_id)  : [];


    // Employee Filter
    if (!empty($employeeIds)) {
        $query->whereIn('employee_id', $employeeIds);
    }

    // Plant Filter
    if (!empty($plantIds)) {
        $query->whereHas('employee', function ($q) use ($plantIds) {
            $q->whereIn('plant_id', $plantIds);
        });
    }

    // IN Gate
    // if (!empty($inGateIds)) {
    //     $query->whereIn('in_geo_fencing_point_id', $inGateIds);
    // }

    // OUT Gate
    // if (!empty($outGateIds)) {
    //     $query->whereIn('out_geo_fencing_point_id', $outGateIds);
    // }
    if (!empty($gatesIds)) {
        $query->whereIn('geo_fencing_point_id', $gateIds);
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
        $employees = Employee::orderByDesc('emp_name')->get();
        $geoFencingPoints = GeoFencingPoint::orderByDesc('location')->get();
        return view('employees.report',[
            'employees'=>$employees,
            'geoFencingPoints' => $geoFencingPoints,
            'plants' => Plant::orderByDesc('name')->get()
        ]);
    }

      public function generateQr($id){
        $employee = Employee::findOrFail($id);
        app(QrService::class)->generate($employee);

        return response()->json([
            'message' => 'QR Code Generated Successfully',
            'qr_url' => asset('storage/'.$employee->qr_code_path)
        ]);
     }

      public function downloadQr($id) {
       $employee = Employee::findOrFail($id);

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

    // public function webShow(){
    //       return view('employees.show');
    // }
    
    public function downloadTemplate(){
       return Excel::download(new EmployeeExport, 'employee_import_template.xlsx');
   }
   
   public function allEmployeeQRCodesDownload()
{
    $employees = Employee::whereNotNull('qr_code_path')->get();
    // return $employees;

    if ($employees->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No QR Codes found for employees.'
        ], 404);
    }

    $zipFileName = 'employee_qr_codes_' . time() . '.zip';
    $zipPath = storage_path('app/public/' . $zipFileName);

    $zip = new ZipArchive;

    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {

        foreach ($employees as $employee) {
            if (!Storage::disk('public')->exists($employee->qr_code_path)) {
                continue;
            }

            $fileContent = Storage::disk('public')->get($employee->qr_code_path);
            $fileName = 'QR_' . ($employee->emp_name ?? 'employee') . '_' . $employee->id . '.png';

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

}
