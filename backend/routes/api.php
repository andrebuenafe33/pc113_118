<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\EmployeeReportController;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Employee;


    // Get authenticated user //
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        // \Log::info('Authenticated user:', [auth()->user()]);
        return $request->user();
    });
    
    // Get user with student //
    Route::middleware('auth:sanctum')->get('/userRole', function (Request $request) {
        return $request->user()->load('student'); 
    });

    // Get all Data //
    Route::middleware('auth:sanctum')->get('/dashboard-stats', function () {
        return response()->json([
            'users' => User::count(),
            'employees' => Employee::count(),
        ]);
    });

    // Dashboard Controller//
    Route::get('/activities/recent-submissions', [DashboardController::class, 'recentSubmissions']);

    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);



    
    Route::middleware('auth:sanctum')->group(function(){ 
        
       
        // Users //    
        Route::get('/usersList', [UsersController::class, 'list']); 
        Route::post('/createusers', [UsersController::class, 'store']);
        Route::get('/get/users/{id}', [UsersController::class, 'edit']);
        Route::get('/getUserById/{id}', [UsersController::class, 'getUserById']);
        Route::get('/getUserProfile', [UsersController::class, 'getUserProfile']); 
        Route::put('/update/users/{id}', [UsersController::class, 'update']);
        Route::post('/update/users/{id}', [UsersController::class, 'update']);
        Route::delete('/delete/users/{id}', [UsersController::class, 'destroy']);

        Route::get('/get/showUser/{id}', [ProfileController::class, 'show']); 

        // Employees //
        Route::get('/employeesList', [EmployeesController::class, 'list']);  
        Route::get('/getEmployeeById/{id}', [EmployeesController::class, 'getEmployeeById']);
        Route::put('/update/employees/{id}', [EmployeesController::class, 'update']); 
        Route::post('/update/employees/{id}', [EmployeesController::class, 'update']);
        Route::get('/employees/export-pdf', [EmployeesController::class, 'exportPDF']);
        
        // Task Assignments
        Route::get('/showTask/{id}', [TaskAssignmentController::class, 'showTask']);
        Route::get('/tasksList', [TaskAssignmentController::class, 'list']);       // List tasks for user
        Route::post('/createtasks', [TaskAssignmentController::class, 'store']);      // Assign a task (admin/manager only)
        Route::patch('/tasks/{id}/status', [TaskAssignmentController::class, 'updateStatus']); // Update status
        Route::put('/tasks/{id}/status', [TaskAssignmentController::class, 'updateStatus']); // Update status
        Route::get('/tasks/getStaff', [TaskAssignmentController::class, 'getStaffUsers']);
        Route::delete('/delete/tasks/{id}', [TaskAssignmentController::class, 'destroy']);

        // Employee Reports
        Route::get('/reports', [EmployeeReportController::class, 'index']);     // List reports of logged-in staff
        Route::post('/reports', [EmployeeReportController::class, 'store']);    // Submit report for a task
       

    }); 

   

   

