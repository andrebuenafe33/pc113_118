<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class EmployeesController extends Controller
{
    public function list()
    {
        try {
            // $employees = Employee::all();
            $employees = Employee::with('user')->get(); 
            return response()->json($employees);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getEmployeeById($id)
    {
        $employee = Employee::with('user')->find($id); // loads employee and user
    
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'employee' => $employee,
            'user' => $employee->user, //include user
        ], 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        Log::info("Update employee attempt for ID: $id", $request->all());

        $employee = Employee::find($id);

        if (!$employee) {
            Log::warning("Employee not found: $id");
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'position' => 'required',
            'department' => 'required',
            'hire_date' => 'required',
            'salary' => 'required',
        ]);

        if ($validator->fails()) {
            Log::error("Validation failed", $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee->update([
            'position' => $request->position,
            'department' => $request->department,
            'hire_date' => $request->hire_date,
            'salary' => $request->salary,
        ]);

        Log::info("Employee updated successfully for ID: $id");

        return response()->json([
            'status' => true,
            'message' => 'Employee updated successfully',
        ]);
    }

    
}
