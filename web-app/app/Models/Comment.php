<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content', 'parent_id'
    ];

    public function commentable() {
        return $this->morphTo();
    }

    public function commenter() {
        return $this->morphTo();
    }

    public function replies() {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    public function parent() {
        return $this->belongsTo(Comment::class, 'parent_id', 'id');
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }
}
