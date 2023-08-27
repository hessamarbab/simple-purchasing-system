<?php

namespace App\Repositories\User;

use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryContract
{
    /**
     * @return Collection
     */
    public function all();

    /**
     * @param string $username
     * @return array
     */
    public function getByUsername(string $username) : array;
}
