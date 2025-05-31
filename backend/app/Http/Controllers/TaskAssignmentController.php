<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskAssignmentController extends Controller
{
    
    public function list()
    {
        $user = Auth::user();

        if (in_array($user->role_id, [1, 2])) {
            // Admin & Manager see tasks they assigned
            $tasks = TaskAssignment::where('assigned_by', $user->id)
                        ->with(['assignee', 'assigner'])
                        ->get();
        } else {
            // Other roles see tasks assigned to them
            $tasks = TaskAssignment::where('assigned_to', $user->id)
                        ->with(['assignee', 'assigner'])
                        ->get();
        }

        return response()->json(['tasks' => $tasks]);
    }


    // Store a new task assignment (only admin or manager)
    public function store(Request $request)
    {
        $user = Auth::user();

         // Only admins and managers can assign tasks
        if (!in_array($user->role_id, [1, 2])) {
            return response()->json(['error' => 'Unauthorized. Only admins or managers can assign tasks.'], 403);
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task = TaskAssignment::create([
            'assigned_by' => $user->id,
            'assigned_to' => $request->assigned_to,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Task assigned successfully', 'task' => $task]);
    }

    // Update task status (e.g. by staff or manager)
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $task = TaskAssignment::findOrFail($id);

        // Only assigned_to (staff) or assigner (admin/manager) can update
        if ($task->assigned_to !== $user->id && $task->assigned_by !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->status = $request->status;
        $task->save();

        return response()->json(['message' => 'Task status updated', 'task' => $task]);
    }

    // Get Staffs
    public function getStaffUsers()
    {
        $staffUsers = User::whereHas('role', function ($query) {
            $query->where('name', 'staff');
        })->select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'staffs' => $staffUsers,
        ]);
    }

    // Show Task
    public function showTask($id)
    {
        $task = TaskAssignment::with(['assigner', 'assignee'])->find($id); // eager load assigner & assignee

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'task' => $task,
        ], 200);
    }

    // Delete Tasks
    public function destroy($id)
    {
        try {
            $task = TaskAssignment::findOrFail($id);
            $task->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Task Deleted Successfully!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }   
    }

}
