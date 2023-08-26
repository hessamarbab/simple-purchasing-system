<?php

namespace App\Repositories\Atomic;

use Illuminate\Support\Facades\DB;

class DbTransactionQueryBuilderRepository implements DbTransactionRepositoryContract
{
    public function beginTransaction()
    {
        DB::beginTransaction();
    }

    public function commit()
    {
        DB::commit();
    }

    public function rollback()
    {
        DB::rollBack();
    }
}
