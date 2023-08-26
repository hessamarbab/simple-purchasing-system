<?php

namespace App\Repositories\Atomic;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DbTransactionRepositoryCacheDecorator implements DbTransactionRepositoryContract
{
    /**
     * @param DbTransactionRepositoryContract $dbTransactionRepository
     */
    public function __construct(
        protected DbTransactionRepositoryContract $dbTransactionRepository = new DbTransactionQueryBuilderRepository()
    ){}
    public function beginTransaction()
    {
        $this->dbTransactionRepository->beginTransaction();
    }

    public function commit()
    {
        $this->dbTransactionRepository->commit();
    }

    public function rollback()
    {
        $this->dbTransactionRepository->rollback();
        Cache::clear();
    }
}
