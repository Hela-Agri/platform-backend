<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\BaseRepository;

class PaymentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'paid_amount',
        'balance',
        'transaction_reference',
        'merchant_request_id',
        'checkout_request_id',
        'transaction_date',
        'payment_mode_id',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Payment::class;
    }
}
