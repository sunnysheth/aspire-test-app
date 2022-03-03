<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\LoanPayment;
use App\Models\UserLoanApplication;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoanApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $applicationData = [
            'loan_amount' =>$this->amount,
            'loan_term' => $this->term,
            'amount_due' => $this->amount_left,
        ];

        $loanRepayments = LoanPayment::where('loan_id', $this->id)->orderBy('created_at', 'ASC')->get();
        $repayments = [];
        $totalAmountPaid = 0;
        $emi = 1;
        foreach($loanRepayments as $repayment) {
            $totalAmountPaid += $repayment->amount;
            $repayments[] = [
                'amount' => $repayment->amount,
                'amount_left' => $repayment->amount_left,
                'pay_date' => Carbon::parse($repayment->created_at)->format('Y-m-d h:i:s'),
                'recursion' => $emi,
            ];
            $emi++;
        }
        $applicationData['amount_paid'] = number_format($totalAmountPaid, 2);
        $applicationData['repayments'] = $repayments;

        return $applicationData;
    }
}
