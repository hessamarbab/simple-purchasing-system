<?php

namespace App\Repositories\Atomic;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Throwable;

class DbTransactionRepositoryCacheDecorator implements DbTransactionRepositoryContract
{
    /**
     * @param DbTransactionRepositoryContract $dbTransactionRepository
     */
    public function __construct(
        protected DbTransactionRepositoryContract $dbTransactionRepository = new DbTransactionQueryBuilderRepository()
    ){}


    /**
     * @return void
     * @throws Throwable
     */
    public function beginTransaction()
    {
        $this->dbTransactionRepository->beginTransaction();
    }


    /**
     * @return void
     * @throws Throwable
     */
    public function commit()
    {
        $this->dbTransactionRepository->commit();
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function rollback()
    {
        $this->dbTransactionRepository->rollback();
        Cache::clear();
    }
}
