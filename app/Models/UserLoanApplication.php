<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLoanApplication extends Model
{
    use SoftDeletes;

    const LOAN_REQUESTED_STATUS = 0;
    const LOAN_APPROVED_STATUS= 1;
    const LOAN_REJECTED_STATUS= 2;
    const LOAN_COMPLETED_STATUS = 3;

    const LOAN_STATUS_COMPLETED_YES = 1;
    const LOAN_STATUS_COMPLETED_NO = 0;

    protected $fillable = [
        'user_id', 'amount', 'term', 'amount_left', 'loan_status', 'is_completed'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'loan_status' => 'boolean',
        'is_completed' => 'boolean',
    ];    
}
