<?php

namespace App\Repositories\User;

use App\Models\User;

class UserEloquentRepository implements UserRepositoryContract
{
    public function all()
    {
        return User::all();
    }

    public function getByUsername(string $username): array
    {
        return User::where('username', $username)->first()->toArray();
    }
}
