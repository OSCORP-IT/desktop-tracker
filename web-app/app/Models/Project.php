<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    use HasFactory;

    protected $guarded = [];

    public function project_team_members() {
        return $this->hasManyThrough(User::class, ProjectTeamMember::class, 'project_id', 'id', 'id', 'user_id');
    }
}
