<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLoanPaymentAPIRequest;
use App\Http\Requests\API\UpdateLoanPaymentAPIRequest;
use App\Models\LoanPayment;
use App\Repositories\LoanPaymentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class LoanPaymentAPIController
 */
class LoanPaymentAPIController extends AppBaseController
{
    private LoanPaymentRepository $loanPaymentRepository;

    public function __construct(LoanPaymentRepository $loanPaymentRepo)
    {
        $this->loanPaymentRepository = $loanPaymentRepo;
    }

    /**
     * Display a listing of the LoanPayments.
     * GET|HEAD /loan-payments
     */
    public function index(Request $request): JsonResponse
    {
        $loanPayments = $this->loanPaymentRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($loanPayments->toArray(), 'Loan Payments retrieved successfully');
    }

    /**
     * Store a newly created LoanPayment in storage.
     * POST /loan-payments
     */
    public function store(CreateLoanPaymentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $loanPayment = $this->loanPaymentRepository->create($input);

        return $this->sendResponse($loanPayment->toArray(), 'Loan Payment saved successfully');
    }

    /**
     * Display the specified LoanPayment.
     * GET|HEAD /loan-payments/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var LoanPayment $loanPayment */
        $loanPayment = $this->loanPaymentRepository->find($id);

        if (empty($loanPayment)) {
            return $this->sendError('Loan Payment not found');
        }

        return $this->sendResponse($loanPayment->toArray(), 'Loan Payment retrieved successfully');
    }

    /**
     * Update the specified LoanPayment in storage.
     * PUT/PATCH /loan-payments/{id}
     */
    public function update($id, UpdateLoanPaymentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var LoanPayment $loanPayment */
        $loanPayment = $this->loanPaymentRepository->find($id);

        if (empty($loanPayment)) {
            return $this->sendError('Loan Payment not found');
        }

        $loanPayment = $this->loanPaymentRepository->update($input, $id);

        return $this->sendResponse($loanPayment->toArray(), 'LoanPayment updated successfully');
    }

    /**
     * Remove the specified LoanPayment from storage.
     * DELETE /loan-payments/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var LoanPayment $loanPayment */
        $loanPayment = $this->loanPaymentRepository->find($id);

        if (empty($loanPayment)) {
            return $this->sendError('Loan Payment not found');
        }

        $loanPayment->delete();

        return $this->sendSuccess('Loan Payment deleted successfully');
    }
}
