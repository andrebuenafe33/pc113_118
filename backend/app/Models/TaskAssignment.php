<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    protected $fillable = [
        'assigned_by',
        'assigned_to',
        'title',
        'description',
        'due_date',
        'status',
    ];

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
