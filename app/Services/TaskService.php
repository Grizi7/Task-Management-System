<?php

namespace App\Services;

use App\Models\Task;
use App\Enums\UserRoleEnum;
use App\Core\Services\BaseService;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Exception;

class TaskService extends BaseService
{
    public function __construct(TaskRepository $taskRepository)
    {
        parent::__construct($taskRepository);
    }

    public function paginate(int $perPage = 15, array $relations = [], array $columns = ['*'], array $filters = [], array $withCount = []): LengthAwarePaginator
    {
        if (isset($filters['due_start'])) {
            $filters['due_start'] = date('Y-m-d', strtotime($filters['due_start']));
            $filters['due_date'] =  ['>=', $filters['due_start']];
        }
        unset($filters['due_start']);

        if (isset($filters['due_end'])) {
            $filters['due_end'] = date('Y-m-d', strtotime($filters['due_end']));
            $filters['due_date'] = ['<=', $filters['due_end']];
        }
        unset($filters['due_end']);

        $user = auth()->user();
        if ($user->role != UserRoleEnum::manager) {
            $filters['assignee_id'] = $user->id;
        }
        return $this->repository->paginate($perPage, $relations, $columns, $filters, $withCount);
    }

    public function getTask(Task $task): ?Task
    {
        if (!$task) {
            return null;
        }

        $user = auth()->user();

        if ($user->role !== UserRoleEnum::manager && $task->assignee_id !== $user->id) {
            throw new AccessDeniedHttpException('You do not have access to this task.');
        }

        $task->load('creator', 'dependencies');
        return $task;
    }

    public function create(array $data): Task
    {
        $data['status'] = 0;
        $data['created_by_id'] = auth()->id();

        $task = $this->repository->create($data);

        return $task->load('creator', 'assignee');
    }
    public function update(Model $task, array $data): Task
    {
        $this->repository->update($task, $data);
        return $task->load('creator', 'dependencies', 'assignee');
    }
    public function updateTaskStatus(Task $task, int $status): Task
    {
        $user = auth()->user();

        if ($user->role !== UserRoleEnum::manager && $task->assignee_id !== $user->id) {
            throw new AccessDeniedHttpException('You do not have permission to update this task.');
        }
        if ($status == 2 && !$task->areDependenciesCompleted()) {
            throw new Exception('Cannot mark task as completed. All dependencies must be completed first.');
        }

        $task->status = $status;
        $task->save();

        return $task->load('creator', 'dependencies', 'assignee');
    }

    public function addDependencies(Task $task, array $dependencyIds): void
    {
        $this->repository->addDependencies($task, $dependencyIds);
    }
    public function removeDependencies(Task $task, array $dependencyIds): void
    {
        $this->repository->removeDependencies($task, $dependencyIds);
    }
}