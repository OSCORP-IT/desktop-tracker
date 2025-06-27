<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskTimeLog extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'start_time',
        'end_time',
        'note'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function task() {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
