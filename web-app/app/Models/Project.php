<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'manager_id',
        'overview',
        'start_date',
        'end_date',
        'budget',
        'thumbnail_image',
        'status',
        'created_by_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function manager() {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function team_members() {
        return $this->hasMany(ProjectTeamMember::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
