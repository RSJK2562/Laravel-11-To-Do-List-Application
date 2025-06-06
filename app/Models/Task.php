<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $fillable = ['title', 'completed'];
    // cast the completed column to a boolean
    protected $casts = [
        'completed' => 'boolean',
    ];
    // Scope a query to only include active (non-completed) tasks.
    public function scopeActive($query)
    {
        return $query->where('completed', false);
    }
    // Scope a query to only include completed tasks.
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }
    // Check if a task with the same title exists (case-insensitive).
    public static function titleExists($title, $excludeId = null)
    {
        $query = static::whereRaw('LOWER(title) = ?', [strtolower(trim($title))]);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    // Toggle the completion status of a task.
    public function toggleCompletion()
    {
        $this->completed = !$this->completed;
        return $this->save();
    }
}
