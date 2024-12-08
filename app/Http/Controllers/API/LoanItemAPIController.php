<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLoanItemAPIRequest;
use App\Http\Requests\API\UpdateLoanItemAPIRequest;
use App\Models\LoanItem;
use App\Repositories\LoanItemRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class LoanItemAPIController
 */
class LoanItemAPIController extends AppBaseController
{
    private LoanItemRepository $loanItemRepository;

    public function __construct(LoanItemRepository $loanItemRepo)
    {
        $this->loanItemRepository = $loanItemRepo;
    }

    /**
     * Display a listing of the LoanItems.
     * GET|HEAD /loan-items
     */
    public function index(Request $request): JsonResponse
    {
        $loanItems = $this->loanItemRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($loanItems->toArray(), 'Loan Items retrieved successfully');
    }

    /**
     * Store a newly created LoanItem in storage.
     * POST /loan-items
     */
    public function store(CreateLoanItemAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $loanItem = $this->loanItemRepository->create($input);

        return $this->sendResponse($loanItem->toArray(), 'Loan Item saved successfully');
    }

    /**
     * Display the specified LoanItem.
     * GET|HEAD /loan-items/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var LoanItem $loanItem */
        $loanItem = $this->loanItemRepository->find($id);

        if (empty($loanItem)) {
            return $this->sendError('Loan Item not found');
        }

        return $this->sendResponse($loanItem->toArray(), 'Loan Item retrieved successfully');
    }

    /**
     * Update the specified LoanItem in storage.
     * PUT/PATCH /loan-items/{id}
     */
    public function update($id, UpdateLoanItemAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var LoanItem $loanItem */
        $loanItem = $this->loanItemRepository->find($id);

        if (empty($loanItem)) {
            return $this->sendError('Loan Item not found');
        }

        $loanItem = $this->loanItemRepository->update($input, $id);

        return $this->sendResponse($loanItem->toArray(), 'LoanItem updated successfully');
    }

    /**
     * Remove the specified LoanItem from storage.
     * DELETE /loan-items/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var LoanItem $loanItem */
        $loanItem = $this->loanItemRepository->find($id);

        if (empty($loanItem)) {
            return $this->sendError('Loan Item not found');
        }

        $loanItem->delete();

        return $this->sendSuccess('Loan Item deleted successfully');
    }
}
