<?php
namespace App\Repositories;

use App\Contracts\UserContract;
use App\Core\Repositories\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository implements UserContract
{
    /**
     * Constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }
}