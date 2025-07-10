<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model {
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'due_time' => 'datetime',
        'completed_at' => 'datetime',
        'sub_tasks' => 'array',
    ];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function assigned_from() {
        return $this->belongsTo(User::class, 'assigned_from_id', 'id');
    }

    public function assigned_to() {
        return $this->belongsTo(User::class, 'assigned_to_id', 'id');
    }

    public function time_logs() {
        return $this->hasMany(TaskTimeLog::class, 'task_id', 'id');
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Ensure format is correct (e.g., "2:30" → "02:30")
    function format_time($time) {
        if (strpos($time, ':') === false) {
            return "00:" . str_pad($time, 2, '0', STR_PAD_LEFT);
        }

        return $time;
    }
}
