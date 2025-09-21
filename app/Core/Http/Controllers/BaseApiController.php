<?php

namespace App\Core\Http\Controllers;

use App\Core\Contracts\ServiceInterface;
use Illuminate\Http\Request;
use App\Core\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class BaseApiController extends Controller
{
    use ApiResponseTrait;

    /**
     * The service instance.
     *
     * @var ServiceInterface
     */
    protected $service;


    /**
     * The model class name.
     *
     * @var mixed
     */

    protected $modelClass;

    /**
     * Constructor.
     *
     * @param ServiceInterface $service
     * @param mixed $modelClass
     */
    public function __construct(ServiceInterface $service, mixed $modelClass)
    {
        $this->service = $service;
        $this->modelClass = $modelClass;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        try {
            $perPage = $request->input('per_page', 15);
            $data = $this->service->paginate($perPage);
            return $this->respondWithCollection($data, $this->modelClass, 'Data retrieved successfully.');
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to fetch data.', [], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResource|JsonResponse
     */
    public function store(Request $request): JsonResource|JsonResponse
    {
        try {
            $model = $this->service->create($request->validated());
            return $this->respondWithResource($model, $this->modelClass, 'Resource created successfully.');
        } catch (Throwable $e) {
            return $this->respondWithError('Failed to create resource.', [], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Model $model
     * @return JsonResource|JsonResponse
     */
    // public function show(Model $model): JsonResource|JsonResponse
    // {
    //     try {
    //         return $this->respondWithResource($model, $this->modelClass, 'Resource retrieved successfully');
    //     } catch (Throwable $e) {
    //         return $this->respondWithError('Failed to fetch resource.', [], 500);
    //     }
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Model $model
     * @return JsonResource|JsonResponse
     */
    // public function update(Request $request, Model $model): JsonResource|JsonResponse
    // {
    //     try {
    //         $this->service->update($model, $request->validated());
    //         return $this->respondWithSuccess('Resource updated successfully');
    //     } catch (Throwable $e) {
    //         return $this->respondWithError('Failed to update resource.', [], 500);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param Model $model
     * @return JsonResponse
     */
    // public function destroy(Model $model): JsonResponse
    // {
    //     try {
    //         $this->service->delete($model);
    //         return $this->respondWithSuccess('Resource deleted successfully');
    //     } catch (Throwable $e) {
    //         return $this->respondWithError('Failed to delete resource.', [], 500);
    //     }
    // }
}
