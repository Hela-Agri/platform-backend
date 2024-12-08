<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentAPIRequest;
use App\Http\Requests\API\UpdatePaymentAPIRequest;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Payment;
use App\Models\Status;
use App\Repositories\PaymentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class PaymentAPIController
 */
class PaymentAPIController extends AppBaseController
{
    private PaymentRepository $paymentRepository;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepository = $paymentRepo;
    }

    /**
     * Display a listing of the Payments.
     * GET|HEAD /payments
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $payments = $this->paymentRepository
            ->with(['payment_mode', 'offTaker'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 50));

        return $this->sendResponse($payments->toArray(), 'Payments retrieved successfully');
    }

    /**
     * Store a newly created Payment in storage.
     * POST /payments
     */
    public function store(CreatePaymentAPIRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $input = $request->all();

            $input['user_id'] = Auth::user()->id;

            $payment = $this->paymentRepository->create($input);

            $this->loanSettlement($request, $payment);
            $input['balance'] = $input['paid_amount'] - $input['allocated_amount'];
            if ($input['balance'] >= 0) {
                $this->paymentRepository->update($input, $payment->id);
            }
            DB::commit();
            return $this->sendResponse($payment->toArray(), 'Payment saved successfully');
        } catch (\Exception $exception) {
            Log::info($exception);
            return $this->sendError('Error while making payment, contact admin');
        }
    }

    /**
     * Display the specified Payment.
     * GET|HEAD /payments/{id}
     * @throws \Exception
     */
    public function show($id): JsonResponse
    {
        /** @var Payment $payment */
        $payment = $this->paymentRepository
            ->with(['payment_mode', 'offTaker', 'loans'])
            ->find($id);

        if (empty($payment)) {
            return $this->sendError('Payment not found');
        }

        return $this->sendResponse($payment->toArray(), 'Payment retrieved successfully');
    }

    /**
     * @param UpdatePaymentAPIRequest $request
     * Update the specified Payment in storage.
     * PUT/PATCH /payments/{id}
     */
    public function update($id, UpdatePaymentAPIRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $input = $request->all();

            $payment = $this->paymentRepository->find($id);

            $this->loanSettlement($request, $payment);
            $update_input['balance'] = $payment->balance - $input['paid_amount'];

            if ($update_input['balance'] > 0) {
                $this->paymentRepository->update($update_input, $payment->id);
            } else {
                $update_input['balance'] = 0;
                $this->paymentRepository->update($update_input, $payment->id);
            }

            DB::commit();
            return $this->sendResponse($payment->toArray(), 'Payment saved successfully');
        } catch (\Exception $exception) {
            Log::info($exception);
            return $this->sendError('Error while making payment, contact admin');
        }
    }

    /**
     * Remove the specified Payment from storage.
     * DELETE /payments/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Payment $payment */
        $payment = $this->paymentRepository->find($id);

        if (empty($payment)) {
            return $this->sendError('Payment not found');
        }

        $payment->delete();

        return $this->sendSuccess('Payment deleted successfully');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null $payment
     * @return void
     */
    public function loanSettlement($request, \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null $payment): void
    {

        if ($request->has('selected_loans')) {
            foreach ($request->get('selected_loans') as $item) {

                $loan = Loan::find($item['id']);
                if ($loan->balance >= $item['repaid_amount']) {
                    $balance = $loan->balance - $item['repaid_amount'];
                    $loan->update([
                        'balance' => $balance
                    ]);

                    if ($loan->balance == 0) {
                        $loan->update([
                            'payment_status_id' => Status::where('code', 'PAID')->first()->id,
                            'status_id' => Status::where('code', 'CLOSED')->first()->id
                        ]);
                    } elseif ($loan->balance > 0 && $loan->balance < $loan->total) {
                        $loan->update([
                            'payment_status_id' => Status::where('code', 'PARTIALLY PAID')->first()->id
                        ]);
                    }
                    LoanPayment::create([
                        'payment_id' => $payment->id,
                        'loan_id' => $item['id'],
                        'amount' => $item['repaid_amount'],
                        'balance' => $balance
                    ]);
                }
            }
        }
    }
}
