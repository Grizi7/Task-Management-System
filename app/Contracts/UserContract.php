<?php
namespace App\Contracts;

interface UserContract
{
    public function getUserByEmail(string $email);
}