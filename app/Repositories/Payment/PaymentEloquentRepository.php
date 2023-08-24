<?php

namespace App\Repositories\Payment;

use App\Models\Payment;

class PaymentEloquentRepository implements PaymentRepositoryContract
{
    public function all()
    {
        return Payment::all();
    }
}
