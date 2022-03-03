<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanPayment;
use App\Models\UserLoanApplication;
use App\Http\Requests\API\LoanApplyRequest;
use App\Http\Resources\UserLoanApplicationResource;

/** @OA\SecurityScheme(
    *     type="http",
    *     description="Login with email and password to get the authentication token",
    *     name="Token based Based",
    *     in="header",
    *     scheme="bearer",
    *     bearerFormat="JWT",
    *     securityScheme="apiAuth",
    * )
*/
class ApplicationController extends Controller
{
    /**
        * @OA\Post(
        * path="/api/loan/apply",
        * operationId="Loan apply",
        * tags={"Apply for a loan"},
        * summary="Apply for a loan",
        * security={{"bearer_token":{}}},
        * description="Apply for a loan",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"amount", "term"},
        *               @OA\Property(property="amount", type="text"),
        *               @OA\Property(property="term", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Loan has been applied successful",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Loan has been applied successful",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
    */ 
    public function apply(LoanApplyRequest $request)
    {
        try {
            $amount = $request->get('amount');
            $term = $request->get('term');
            $loanInterest = config('config-variables.interest_rate');
            $getEMIAmount = self::calculateEMI($amount, $loanInterest, $term);

            $userLoanApplication = new UserLoanApplication();
            $userLoanApplication->user_id = $request->user()->id;
            $userLoanApplication->amount = $request->amount;
            $userLoanApplication->term = $request->term;
            $userLoanApplication->amount_left = $request->amount;
            $userLoanApplication->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'loan_id' => $userLoanApplication->id,
                    'loan_info' => (new UserLoanApplicationResource($userLoanApplication)),
                    'amount_per_week' => $getEMIAmount,
                ],
                'message' => "'Loan has been applied successful!', 'Your loan has been sent for approval, you will receive a confirmation post approval.'"
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to apply for loan!'
            ], 404);
        }
    }

    /**
        * @OA\Post(
        * path="/api/loan/{id}/approve",
        * operationId="Approve a loan",
        * tags={"Approve a loan"},
        * summary="Approve a loan",
        * description="Approve a loan",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"id"},
        *               @OA\Property(property="id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Loan has been approved successful",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Loan has been approved successful",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
    */
    public function approve($id)
    {
        try {
            $userLoanApplication = UserLoanApplication::find($id);
            if (!$userLoanApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found..'
                ], 404);
            }
    
            if ($userLoanApplication->loan_status == UserLoanApplication::LOAN_REQUESTED_STATUS) {
                $userLoanApplication
                    ->update(['loan_status' => UserLoanApplication::LOAN_APPROVED_STATUS]);
    
                return response()->json([
                    'success' => true,
                    'data' => (new UserLoanApplicationResource($userLoanApplication)),
                    'message' => 'Loan has been approved successfully.'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
        * @OA\Post(
        * path="/api/loan/{id}/reject",
        * operationId="Reject a loan",
        * tags={"Reject a loan"},
        * summary="Reject a loan",
        * description="Reject a loan",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"id"},
        *               @OA\Property(property="id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Loan has been rejected successful",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Loan has been rejected successful",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
    */
    public function reject($id)
    {
        try {
            $userLoanApplication = UserLoanApplication::find($id);
            if (!$userLoanApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found..'
                ], 404);
            }
    
            if ($userLoanApplication->loan_status == UserLoanApplication::LOAN_REQUESTED_STATUS) {
                $userLoanApplication
                    ->update(['loan_status' => UserLoanApplication::LOAN_REJECTED_STATUS]);
    
                return response()->json([
                    'success' => true,
                    'data' => (new UserLoanApplicationResource($userLoanApplication)),
                    'message' => 'Loan has been rejected.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reject a loan application which is not in REQUESTED state.'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
        * @OA\Post(
        * path="/api/loan/{id}/pay-emi",
        * operationId="Pay loan EMI",
        * tags={"Pay loan EMI"},
        * summary="Pay loan EMI",
        * description="Pay loan EMI",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"id", "payment"},
        *               @OA\Property(property="id", type="text"),
        *               @OA\Property(property="payment", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description= "Payment done successfully!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description= "Payment done successfully!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
    */
    public function payLoanEMI(Request $request, $id)
    {
        try {
            $userLoanApplication = UserLoanApplication::find($id);
            if (!$userLoanApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found..',
                ], 404);
            }

            $repayAmount = $request->payment;
            if ($userLoanApplication->loan_status == UserLoanApplication::LOAN_APPROVED_STATUS && $userLoanApplication->is_completed == UserLoanApplication::LOAN_STATUS_COMPLETED_NO) {
                if ($userLoanApplication->amount_left < $repayAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => sprintf('You are left with amount %.02f and trying to pay amount %.02f which is more than loan repay amount left.Please input payment manually', $userLoanApplication->amount_left, $repayAmount),
                    ]);
                }

                LoanPayment::create([
                    'loan_id' => $id,
                    'amount' => $repayAmount,
                    'amount_left' => $userLoanApplication->amount_left - $repayAmount,
                ]);

                // update user loan application with reduced amount left
                $userLoanApplication->amount_left = $userLoanApplication->amount_left - $repayAmount;
                if ($userLoanApplication->amount_left <= 0) {
                    $userLoanApplication->is_completed = UserLoanApplication::LOAN_STATUS_COMPLETED_YES;
                    $userLoanApplication->loan_status = UserLoanApplication::LOAN_COMPLETED_STATUS;
                }
                $userLoanApplication->save();

                return response()->json([
                    'success' => true,
                    'data' => (new UserLoanApplicationResource($userLoanApplication)),
                    'message' => 'Payment done successfully!'
                ]);                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot accept payment for application which is not approved.',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * Private method to calculat installment/repayment amount based on amount, rate and term specified.
     *
     * @param $amount: principal amount of loan
     * @param $rate : presumed to be yearly rate of interest (derived from config generally)
     * @param $term : presumed to be number of weeks
     * @return float
     */
    private function calculateEMI($amount, $rate, $term)
    {
        return ceil($amount / $term);
    }    
}
