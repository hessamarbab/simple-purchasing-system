<?php

namespace App\Repositories\Atomic;

interface DbTransactionRepositoryContract
{
    /**
     * @return void
     */
    public function beginTransaction();

    /**
     * @return void
     */
    public function commit();

    /**
     * @return void
     */
    public function rollback();
}
