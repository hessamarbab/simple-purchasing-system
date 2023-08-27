<?php

namespace App\Repositories\User;

use Illuminate\Support\Facades\Cache;

class UserRepositoryCachingDecorator implements UserRepositoryContract
{
    const USER_CACHE_PREFIX = 'USER_';
    const GET_BY_USERNAME = "BY_USERNAME_";

    /**
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(
        protected int $ttl = 60,
        protected UserRepositoryContract $userRepository = new UserEloquentRepository()
    ){}

    public function all()
    {
        // it's not ok to cache all things
        return $this->userRepository->all();
    }

    public function getByUsername(string $username): array
    {
        $cacheKey = self::USER_CACHE_PREFIX . self::GET_BY_USERNAME . $username;
        return Cache::remember(
            $cacheKey,
            $this->ttl,
            function () use ($username) {
                return $this->userRepository->getByUsername($username);
            }
        );
    }
}
