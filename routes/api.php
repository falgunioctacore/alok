<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendenaceController;
use App\Http\Controllers\Auth\AuthEmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\GeoFencingPointController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\SiteArea;
use App\Http\Controllers\SiteAreaController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleAttendanceController;
use App\Models\Attendance;
use App\Models\GeoFencingPoint;
use Illuminate\Support\Facades\Route;

Route::get('/employees/download-all-qrcodes', [EmployeeController::class, 'allEmployeeQRCodesDownload']);

// Route::get('/vehicles/download-all-qrcodes', [VehicleController::class, 'downloadAllQRCodes']);
Route::get('/vehicles/download-all-qrcodes', [VehicleController::class, 'downloadAllQRCodesPDF']);




Route::apiResource('plants', PlantController::class);
Route::apiResource('access-points', GeoFencingPointController::class);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('vehicles', VehicleController::class);
Route::apiResource('departments',DepartmentController::class);
Route::apiResource('site-areas',SiteAreaController::class);
Route::apiResource('attendances', AttendenaceController::class);

Route::post('/employee/login',[AuthEmployeeController::class,'login']);
Route::get('/employee/attendance/history',[AttendenaceController::class,'monthRecords']);
Route::get('/employee/attendance/index',[AttendenaceController::class,'employeeIndex']);
Route::post('/employee/attendance/mark',[AttendenaceController::class,'mark'])->name('attendance.mark');
Route::get('/employee/attendence/report',[EmployeeController::class,'report'])->name('attendance.report');
Route::get('/employees/{id}/e', [EmployeeController::class, 'showWebPage']);
Route::get('/employees/{id}/download-qr', [EmployeeController::class, 'downloadQr']);
Route::post('/employees/{id}/generate-qr', [EmployeeController::class, 'generateQr']);


Route::get('/vehicle/attendance/history',[VehicleAttendanceController::class,'monthRecords']);
Route::get('/vehicle/attendance/index',[VehicleAttendanceController::class,'vehicleIndex']);
Route::post('/vehicle/attendance/mark',[VehicleAttendanceController::class,'mark'])->name('attendance.mark');
Route::get('/vehicle/attendence/report',[VehicleController::class,'report'])->name('vehicle.attendance.report');

Route::get('/vehicles/{id}/e', [VehicleController::class, 'showWebPage']);

Route::post('/trash/{model}/{id}/restore', [TrashController::class, 'restore'])
    ->name('api.trash.restore');

Route::delete('/trash/{model}/{id}/delete', [TrashController::class, 'forceDelete'])
    ->name('api.trash.delete');


// Route::middleware('auth:sanctum')->group(function () {
// Route::prefix('employees')->group(function(){

// });
// });

// Route::apiResource('inout-reasons', InOutReasonController::class);

// Route::post('/me',[AuthEmployeeController::class,'me'])

?>