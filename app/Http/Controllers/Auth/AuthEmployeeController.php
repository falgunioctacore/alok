<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeoFencingPoinRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthEmployeeController extends Controller
{     
      public function showLoginForm(){
        return view('emp_auth.login');
      }
     public function login(Request $request)
    {
        $request->validate([
            'emp_code' => 'required|string',
            'password' => 'required|string'
        ]);

        $employee = User::where('emp_code', $request->emp_code)->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Employee Code or Password'], 401);
        }

        $token = $employee->createToken('alok_ind')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'employee' => $employee
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
    }

     public function me(Request $request){
       return response()->json([
        'bearerToken' => $request->bearerToken(),
        'user' => $request->user(),
        'user1'=>Auth::user(),
    ]);
     }



}
