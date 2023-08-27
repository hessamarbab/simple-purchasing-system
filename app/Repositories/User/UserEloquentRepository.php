<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserEloquentRepository implements UserRepositoryContract
{
    /**
     * @return Collection
     */
    public function all()
    {
        return User::all();
    }

    /**
     * @param string $username
     * @return array
     */
    public function getByUsername(string $username): array
    {
        return User::where('username', $username)->first()->toArray();
    }
}
