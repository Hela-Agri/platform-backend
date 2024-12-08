<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLoanAPIRequest;
use App\Http\Requests\API\UpdateLoanAPIRequest;
use App\Models\FarmActivity;
use App\Models\Loan;
use App\Repositories\LoanRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class LoanAPIController
 */
class LoanAPIController extends AppBaseController
{
    private LoanRepository $loanRepository;

    public function __construct(LoanRepository $loanRepo)
    {
        $this->loanRepository = $loanRepo;
    }

    /**
     * Display a listing of the Loans.
     * GET|HEAD /loans
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {

        $loans = $this->loanRepository
            ->with(['walletTransaction', 'farmer', 'status'])
            ->when($request->has('date_range') && $request->get('date_range') !== null, function ($query) use ($request) {
                $date_range = explode(',', $request->get('date_range'));
                return $query->whereDate('created_at', '>=', $date_range[0])->whereDate('created_at', '<=', $date_range[1]);
            })
            ->when($request->has('farmer_id') && $request->get('farmer_id') !== null, function ($query) use ($request) {
                $query->whereHas('farmer', function ($q1) use ($request) {
                    return $q1->where('id', $request->get('farmer_id'));
                });
            })
            ->when($request->has('code') && $request->get('code') !== null, function ($query) use ($request) {
                return $query->where('code', $request->get('code'));
            })
            ->when($request->has('balance') && $request->get('balance') == true, function ($query) use ($request) {
                return $query->where('balance', '>', 0);
            })
            ->when($request->has('off_taker_id') && $request->get('off_taker_id') !== null, function ($query) use ($request) {
                $query->whereHas('walletTransaction', function ($q1) use ($request) {
                    $q1->whereHas('wallet', function ($q2) use ($request) {
                        return $q2->where('user_id', $request->get('off_taker_id'));
                    });
                });
            })
            ->when($request->has('cohort_id') && $request->get('cohort_id') !== null, function ($query) use ($request) {
                $query->whereHas('farm_activity', function ($q1) use ($request) {
                    return $q1->where('cohort_id', $request->get('cohort_id'));
                });
            })
            ->paginate($request->get('limit', 50));

        return $this->sendResponse($loans->toArray(), 'Loans retrieved successfully');
    }

    /**
     * Store a newly created Loan in storage.
     * POST /loans
     */
    public function store(CreateLoanAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $loan = $this->loanRepository->create($input);

        return $this->sendResponse($loan->toArray(), 'Loan saved successfully');
    }

    /**
     * Display the specified Loan.
     * GET|HEAD /loans/{id}
     * @throws \Exception
     */
    public function show($id): JsonResponse
    {
        /** @var Loan $loan */
        $loan = $this->loanRepository
            ->with(['walletTransaction', 'farmer', 'status', 'items'])
            ->find($id);

        if (empty($loan)) {
            return $this->sendError('Loan not found');
        }

        return $this->sendResponse($loan->toArray(), 'Loan retrieved successfully');
    }

    /**
     * Update the specified Loan in storage.
     * PUT/PATCH /loans/{id}
     */
    public function update($id, UpdateLoanAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Loan $loan */
        $loan = $this->loanRepository->find($id);

        if (empty($loan)) {
            return $this->sendError('Loan not found');
        }

        $loan = $this->loanRepository->update($input, $id);

        return $this->sendResponse($loan->toArray(), 'Loan updated successfully');
    }

    public function approveLoanByCohort($cohort_id, Request $request)
    {

        $validated = $request->validate([
            'approval_date' => 'required|date'
        ]);
        $formatted_date = \Carbon\Carbon::parse($request->approval_date)->format('Y-m-d');




        Loan::with(['farm_activity'])

            ->whereHas('farm_activity', function ($q) use ($cohort_id) {
                return $q->where('cohort_id', '=', $cohort_id);
            })
            ->chunk(1000, function ($loans) use ($request, $formatted_date) {
                foreach ($loans as $loan) {

                    $maturity_date = \Carbon\Carbon::parse($request->approval_date)->addDays($loan->farm_activity->package->duration)->format('Y-m-d');
                    // apply some action to the chunked results here
                    $loan->update(['approval_date' => $formatted_date, 'maturity_date' => $maturity_date]);


                }
            });

        return $this->sendSuccess('Loan approval dates updated successfully');

    }

    /**
     * Remove the specified Loan from storage.
     * DELETE /loans/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Loan $loan */
        $loan = $this->loanRepository->find($id);

        if (empty($loan)) {
            return $this->sendError('Loan not found');
        }

        $loan->delete();

        return $this->sendSuccess('Loan deleted successfully');
    }
}
