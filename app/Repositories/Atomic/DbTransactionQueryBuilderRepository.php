<?php

namespace App\Repositories\Atomic;

use Illuminate\Support\Facades\DB;

class DbTransactionQueryBuilderRepository implements DbTransactionRepositoryContract
{
    /**
     * @return void
     * @throws \Throwable
     */
    public function beginTransaction()
    {
        DB::beginTransaction();
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function commit()
    {
        DB::commit();
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function rollback()
    {
        DB::rollBack();
    }
}
