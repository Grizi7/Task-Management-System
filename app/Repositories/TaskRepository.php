<?php

namespace App\Repositories;
use App\Models\Task;
use App\Contracts\TaskContracts;
use App\Core\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository extends BaseRepository implements TaskContracts
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function addDependencies(Task $task, array $dependencyIds): void
    {
        $task->dependencies()->syncWithoutDetaching($dependencyIds);
    }

    public function getDependencies(Task $task): Collection
    {
        return $task->dependencies;
    }

    public function removeDependencies(Task $task, array $dependencyIds): void
    {
        $task->dependencies()->detach($dependencyIds);
    }
}