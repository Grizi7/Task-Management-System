<?php

namespace App\Contracts;

use App\Core\Contracts\RepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskContracts extends RepositoryInterface
{
    public function addDependencies(Task $task, array $dependencyIds): void;
    public function getDependencies(Task $task): Collection;
    public function removeDependencies(Task $task, array $dependencyIds): void;
}