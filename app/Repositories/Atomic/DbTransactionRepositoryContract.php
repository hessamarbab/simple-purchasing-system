<?php

namespace App\Repositories\Atomic;

use PhpParser\Builder\Interface_;

interface DbTransactionRepositoryContract
{
    public function beginTransaction();

    public function commit();

    public function rollback();
}
