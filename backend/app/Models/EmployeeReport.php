<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeReport extends Model
{
    protected $fillable = [
        'task_assignment_id',
        'submitted_by',
        'report',
        'attachment',
    ];

    public function task()
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
