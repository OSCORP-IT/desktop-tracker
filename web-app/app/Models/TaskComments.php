<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComments extends Model {
    use HasFactory;

    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function task_comments() {
        return $this->hasMany(TaskComments::class, 'task_comment_id', 'id');
    }

    public function task_comment() {
        return $this->belongsTo(TaskComments::class, 'task_comment_id', 'id');
    }
}
