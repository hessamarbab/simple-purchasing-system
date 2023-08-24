<?php

namespace App\Repositories\User;

class UserRepositoryCachingDecorator implements UserRepositoryContract
{

    /**
     * @param UserEloquentRepository $userRepository
     */
    public function __construct(
        protected UserEloquentRepository $userRepository,
        protected int $ttl = 60
    ){}

    public function all()
    {
        // it's not ok to cache all things
        return $this->userRepository->all();
    }
}
