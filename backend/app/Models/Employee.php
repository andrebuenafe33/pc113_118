<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $table = "employees";

    protected $fillable = [
        'user_id',
        'position',
        'department',
        'hire_date',
        'salary'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
