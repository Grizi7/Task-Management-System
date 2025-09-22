<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Api\UserResource;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Core\Http\Controllers\BaseApiController;
use Throwable;

class LoginController extends BaseApiController
{
    /**
     * Constructor.
     *
     * @param AuthService $service
     */
    public function __construct(AuthService $service)
    {
        parent::__construct($service, null);
    }

    public function __invoke(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->service->login($request->validated());
            $data['user'] = new UserResource($data['user']);
            return $this->respondWithSuccess('Login successful.', $data);
        } catch (Throwable $e) {
            return $this->respondWithError('Login failed.', [$e->getMessage()], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $user = auth()->user();
            if ($user) {
                $user->tokens()->delete();
                return $this->respondWithSuccess('Logout successful.');
            }
            return $this->respondWithError('User not found.', [], 404);
        } catch (Throwable $e) {
            return $this->respondWithError('Logout failed.', [$e->getMessage()], 500);
        }
    }    
    
}
