<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLoanPackageAPIRequest;
use App\Http\Requests\API\UpdateLoanPackageAPIRequest;
use App\Models\LoanPackage;
use App\Repositories\LoanPackageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\FarmActivity;

/**
 * Class LoanPackageAPIController
 */
class LoanPackageAPIController extends AppBaseController
{
    private LoanPackageRepository $loanPackageRepository;

    public function __construct(LoanPackageRepository $loanPackageRepo)
    {
        $this->loanPackageRepository = $loanPackageRepo;
    }

    /**
     * Display a listing of the LoanPackages.
     * GET|HEAD /loan-packages
     */
    public function index(Request $request): JsonResponse
    {
        $loanPackages = $this->loanPackageRepository->paginate($request->get('limit', 50));

        return $this->sendResponse($loanPackages->toArray(), 'Loan Packages retrieved successfully');
    }

    /**
     * Store a newly created LoanPackage in storage.
     * POST /loan-packages
     */
    public function store(CreateLoanPackageAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $loanPackage = $this->loanPackageRepository->create($input);

        return $this->sendResponse($loanPackage->toArray(), 'Loan Package saved successfully');
    }

    /**
     * Display the specified LoanPackage.
     * GET|HEAD /loan-packages/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var LoanPackage $loanPackage */
        $loanPackage = $this->loanPackageRepository->find($id);

        if (empty($loanPackage)) {
            return $this->sendError('Loan Package not found');
        }

        return $this->sendResponse($loanPackage->toArray(), 'Loan Package retrieved successfully');
    }

    /**
     * Update the specified LoanPackage in storage.
     * PUT/PATCH /loan-packages/{id}
     */
    public function update($id, UpdateLoanPackageAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var LoanPackage $loanPackage */
        $loanPackage = $this->loanPackageRepository->find($id);
        //check if the loan package is attached to an activity and prevent editting
        if(FarmActivity::where('loan_package_id',$loanPackage->id)->count()){
            return $this->sendError('Loan Package cannot be editted. It is attached to an existing farm activity');
        }
        if (empty($loanPackage)) {
            return $this->sendError('Loan Package not found');
        }

        $loanPackage = $this->loanPackageRepository->update($input, $id);

        return $this->sendResponse($loanPackage->toArray(), 'LoanPackage updated successfully');
    }

    /**
     * Remove the specified LoanPackage from storage.
     * DELETE /loan-packages/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var LoanPackage $loanPackage */
        $loanPackage = $this->loanPackageRepository->find($id);

        if (empty($loanPackage)) {
            return $this->sendError('Loan Package not found');
        }

        $loanPackage->delete();

        return $this->sendSuccess('Loan Package deleted successfully');
    }
}
