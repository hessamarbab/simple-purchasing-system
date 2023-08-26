<?php

namespace App\Repositories\User;

use Illuminate\Database\Eloquent\Model;

interface UserRepositoryContract
{
    public function all();

    public function getByUsername(string $username) : array;
}
