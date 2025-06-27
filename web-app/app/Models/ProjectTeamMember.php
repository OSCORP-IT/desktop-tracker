<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTeamMember extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'assigned_to_id',
        'role'
    ];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function member() {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }
}
