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


class UsersController extends Controller
{
    
    public function list()
    {
        try {
            $users = User::all();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|integer',
            'profile_image' => 'nullable|image|max:2048',
        ]); 

        // Upload profile image if present
        if ($request->hasFile('profile_image')) {
            $filename = time() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $path = $request->file('profile_image')->storeAs('profile_images', $filename, 'public');
            $validated['profile_image'] = $path;
        }

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        // If role_id is 2 or 3, also create a Employee record
        if ((int) $user->role_id === 2 || $user->role_id === 3) {
            try {
                Employee::create([
                    'user_id' => $user->id,
                    'position' => $request->input('position', ''),
                    'department' => $request->input('department', ''),
                    'hire_date' => $request->input('hire_date', ''),
                    'salary' => $request->input('salary', '')
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create employee:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => false,
                    'message' => 'User created, but failed to create in employees table: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
        ]);
    }


    public function update(Request $request, $id)
    {
        try {
            Log::info("Update attempt for User ID: {$id}");

            $user = User::findOrFail($id);

            // Log user info before updating
            Log::info("User fetched: ", $user->toArray());

            $validateUser = Validator::make($request->all(), [
                'name' => 'nullable',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'role_id' => 'nullable|exists:roles,id',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10048',
                'password' => 'nullable|string|min:6',
            ]);

            if ($validateUser->fails()) {
                Log::error("Validation failed for User ID: {$id}", $validateUser->errors()->toArray());
                return response()->json([
                    'status' => false,
                    'message' => 'Update Failed!',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            $updateData = [];
            $previousRole = $user->role_id;

            if ($request->filled('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->filled('email')) {
                $updateData['email'] = $request->email;
            }

            if ($request->filled('role_id')) {
                $updateData['role_id'] = $request->role_id;
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Profile image handling
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $filename);

                if ($user->profile_image && file_exists(public_path('images/' . $user->profile_image))) {
                    unlink(public_path('images/' . $user->profile_image));
                }

                $updateData['profile_image'] = $filename;
            }

            // Log the data to be updated
            Log::info("Update data: ", $updateData);

            // Update user record
            $user->update($updateData);

            // Log the user after update
            Log::info("User after update: ", $user->toArray());

            $newRole = $updateData['role'] ?? $previousRole;

            // Handle role change
            if ($previousRole != 2 && $newRole == 2 || $previousRole != 3 && $newRole == 3) {
                Employee::create([
                    'user_id' => $user->id,
                    'position' => '',
                    'department' => '',
                    'hire_date' => '',
                    'salary' => '',
                ]);
                Log::info("New Employee record created for User ID: {$user->id}");
            }

            if ($previousRole == 2 && $newRole != 2 || $previousRole == 3 && $newRole != 3) {
                Employee::where('user_id', $user->id)->delete();
                Log::info("Employee record deleted for User ID: {$user->id}");
            }

            // Update employee details if still a employee
            if ($newRole == 2 || $newRole == 3) {
                try {
                    $employee = Employee::where('user_id', $user->id)->first();
                    if ($employee) {
                        $employee->update([
                            'position' => $request->input('position', $employee->position),
                            'department' => $request->input('department', $employee->department),
                            'hire_date' => $request->input('hire_date', $employee->hire_date),
                            'salary' => $request->input('salary', $employee->salary),
                        ]);
                        Log::info("Employee record updated for User ID: {$user->id}");
                    }
                } catch (\Throwable $e) {
                    Log::error("Error updating Employee data for User ID: {$user->id}", ['error' => $e->getMessage()]);
                    return response()->json([
                        'status' => false,
                        'message' => 'User updated but employee data failed: ' . $e->getMessage()
                    ], 500);
                }
            }

            Log::info("User update successful for User ID: {$user->id}");

            return response()->json([
                'status' => true,
                'message' => 'User Updated Successfully',
                'user' => $user
            ], 200);

        } catch (\Throwable $th) {
            Log::error("Error in updating user: {$th->getMessage()}");
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }
    
        return response()->json($user, 200);
    }

   
    public function getUserById($id)
    {
        $user = User::with('employee')->find($id); // Eager load the related employee data
        // $user = User::findOrFail($id);

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

    public function getUserProfile(Request $request)
    {
        // Assuming the user is authenticated
        $user = $request->user();

        // Load the staff details if the role is 3
        if ($user->role_id == 3) {
            $user->load('employee');
        }

        return response()->json($user);
    }


    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'User Deleted Successfully!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }   
    }

}
