<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCohortAPIRequest;
use App\Http\Requests\API\UpdateCohortAPIRequest;
use App\Models\Cohort;
use App\Repositories\CohortRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class CohortAPIController
 */
class CohortAPIController extends AppBaseController
{
    private CohortRepository $cohortRepository;

    public function __construct(CohortRepository $cohortRepo)
    {
        $this->cohortRepository = $cohortRepo;
    }

    /**
     * Display a listing of the Cohorts.
     * GET|HEAD /cohorts
     */
    public function index(Request $request): JsonResponse
    {
        $cohorts = $this->cohortRepository->withCount('farmActivities')->paginate($request->get('limit', 200));

        return $this->sendResponse($cohorts->toArray(), 'Cohorts retrieved successfully');
    }

    /**
     * Store a newly created Cohort in storage.
     * POST /cohorts
     */
    public function store(CreateCohortAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $cohort = $this->cohortRepository->create($input);

        return $this->sendResponse($cohort->toArray(), 'Cohort saved successfully');
    }

    /**
     * Display the specified Cohort.
     * GET|HEAD /cohorts/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Cohort $cohort */
        $cohort = $this->cohortRepository->find($id);

        if (empty($cohort)) {
            return $this->sendError('Cohort not found');
        }

        return $this->sendResponse($cohort->toArray(), 'Cohort retrieved successfully');
    }

    /**
     * Update the specified Cohort in storage.
     * PUT/PATCH /cohorts/{id}
     */
    public function update($id, UpdateCohortAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Cohort $cohort */
        $cohort = $this->cohortRepository->find($id);

        if (empty($cohort)) {
            return $this->sendError('Cohort not found');
        }

        $cohort = $this->cohortRepository->update($input, $id);

        return $this->sendResponse($cohort->toArray(), 'Cohort updated successfully');
    }

    /**
     * Remove the specified Cohort from storage.
     * DELETE /cohorts/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Cohort $cohort */
        $cohort = $this->cohortRepository->find($id);

        if (empty($cohort)) {
            return $this->sendError('Cohort not found');
        }

        $cohort->delete();

        return $this->sendSuccess('Cohort deleted successfully');
    }
}
