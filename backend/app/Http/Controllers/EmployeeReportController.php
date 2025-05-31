<?php

namespace App\Http\Controllers;

use App\Models\EmployeeReport;
use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeReportController extends Controller
{
    // List reports for the logged-in staff
    public function index()
    {
        $user = Auth::user();

        if ($user->role->name !== 'staff') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reports = EmployeeReport::where('submitted_by', $user->id)->with('task')->get();

        return response()->json(['reports' => $reports]);
    }

    // Submit a report for a task
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name !== 'staff') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'task_assignment_id' => 'required|exists:task_assignments,id',
            'report' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        // Check if task belongs to staff
        $task = TaskAssignment::where('id', $request->task_assignment_id)
                              ->where('assigned_to', $user->id)
                              ->first();

        if (!$task) {
            return response()->json(['error' => 'Invalid task assignment'], 403);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('reports', 'public');
        }

        $report = EmployeeReport::create([
            'task_assignment_id' => $request->task_assignment_id,
            'submitted_by' => $user->id,
            'report' => $request->report,
            'attachment' => $attachmentPath,
        ]);

        return response()->json(['message' => 'Report submitted successfully', 'report' => $report]);
    }
}
