<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatusEnum;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assignee_id',
        'created_by_id',
    ];

    protected $casts = [
        'status' => TaskStatusEnum::class,
        'due_date' => 'datetime',
    ];

}
