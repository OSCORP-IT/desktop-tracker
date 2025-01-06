<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTeamMember extends Model {
    use HasFactory;

    protected $guarded = [];

    static $status = [
        "Pending", "Approved", "Rejected"
    ];
}
