<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_id', 'amount', 'amount_left'
    ];
}
