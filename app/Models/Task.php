<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A task belonging to a {@see Project}, ordered by {@see Task::$priority} within that project.
 */
class Task extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'priority',
    ];

    /**
     * Project that owns this task.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
