<?php
namespace App\Contracts;

use App\Core\Contracts\RepositoryInterface;

interface UserContract extends RepositoryInterface
{
    public function getUserByEmail(string $email);
}