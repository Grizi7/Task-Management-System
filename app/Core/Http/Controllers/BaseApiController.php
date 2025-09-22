<?php

namespace App\Core\Http\Controllers;

use App\Core\Contracts\ServiceInterface;
use Illuminate\Http\Request;
use App\Core\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
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
}
