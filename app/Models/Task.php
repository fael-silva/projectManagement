<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'title', 'description', 'created_at', 'completed_at', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
