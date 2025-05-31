<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;

class ProfileController extends Controller
{

    public function show(string $id)
    {
        $user = User::with('employee')->find($id); // Eager load the related employee data
       

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'user' => $user,              // includes base user info
            'employee' => $user->employee   // nullable if not a employee
        ], 200);
    }

}
