<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory;

    protected $guarded = [];

    static $priorities = [
        "High", "Medium", "Low"
    ];

    static $status = [
        "Pending", "In Progress", "Review", "Completed"
    ];

    public function assigned_user() {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function task_comments() {
        return $this->hasMany(TaskComments::class, 'task_id', 'id');
    }
}
