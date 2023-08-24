<?php

namespace App\Repositories\User;

use App\Models\User;

class UserEloquentRepository implements UserRepositoryContract
{
    public function all()
    {
        return User::all();
    }
}
