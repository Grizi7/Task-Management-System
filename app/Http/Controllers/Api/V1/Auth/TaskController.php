<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Throwable;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\TaskRequest;
use App\Http\Resources\Api\TaskResource;
use App\Core\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\TaskDependencyRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\Api\UpdateTaskStatusRequest;

class TaskController extends BaseApiController
{
    /**
     * Constructor.
     *
     * @param TaskService $service
     */
    public function __construct(TaskService $service)
    {
        parent::__construct($service, TaskResource::class);
    }

    public function index(Request $request): mixed
    {
        try {
            $filters = $request->only(['status', 'due_start', 'due_end', 'assignee_id']);
            $perPage = $request->get('per_page', 15);
            $tasks = $this->service->paginate($perPage, ['creator', 'dependencies', 'assignee'], ['*'], $filters);
            return $this->respondWithCollection($tasks, TaskResource::class);
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to fetch tasks', [$e->getMessage()], 500);
        }
    }

    public function store(TaskRequest $request): JsonResource|JsonResponse
    {
        try {
            $task = $this->service->create($request->validated());
            return $this->respondWithResource($task, TaskResource::class, 'Task created successfully.');
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to create task', [$e->getMessage()], 500);
        }
    }

    public function show(Task $task): JsonResource|JsonResponse
    {
        try {
            $task = $this->service->getTask($task);
            if (!$task) {
                return $this->respondWithError('Task not found', [], 404);
            }
            return $this->respondWithResource($task, TaskResource::class);
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to fetch task', [$e->getMessage()], 500);
        }
    }

    public function update(TaskRequest $request, Task $task): JsonResource|JsonResponse
    {
        try {
            $task = $this->service->update($task, $request->validated());
            return $this->respondWithResource($task, TaskResource::class, 'Task updated successfully.');
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to update task', [$e->getMessage()], 500);
        }
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResource|JsonResponse
    {
        try {
            $task = $this->service->updateTaskStatus($task, $request->status);
            return $this->respondWithResource($task, TaskResource::class, 'Task status updated successfully.');
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to update task status', [$e->getMessage()], 500);
        }
    }

    public function addDependencies(TaskDependencyRequest $request, Task $task): JsonResource|JsonResponse
    {
        try {
            $this->service->addDependencies($task, $request->validated()['dependency_ids']);
            return $this->respondWithResource($task->load('dependencies'), TaskResource::class, 'Dependencies added.');
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to add dependencies', [$e->getMessage()], 500);
        }
    }

    public function removeDependencies(TaskDependencyRequest $request, Task $task): JsonResource|JsonResponse
    {
        try {
            $this->service->removeDependencies($task, $request->validated()['dependency_ids']);
            return $this->respondWithResource($task->load('dependencies'), TaskResource::class, 'Dependencies removed.');
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to remove dependencies', [$e->getMessage()], 500);
        }
    }

    public function destroy(Task $task): JsonResponse
    {
        try {
            $task->delete();
            return $this->respondWithSuccess('Task deleted successfully.');
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to delete task', [$e->getMessage()], 500);
        }
    }
}
