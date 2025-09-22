<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\Repositories\UserRepository;
use Exception;


class AuthService extends BaseService
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    public function login(array $request): mixed
    {
        $user = $this->repository->getUserByEmail($request['email']);
        if (!$user || !password_verify($request['password'], $user->password)) {
            throw new Exception('Invalid credentials.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }
}