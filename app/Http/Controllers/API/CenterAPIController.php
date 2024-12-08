<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCenterAPIRequest;
use App\Http\Requests\API\UpdateCenterAPIRequest;
use App\Models\Center;
use App\Repositories\CenterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class CenterAPIController
 */
class CenterAPIController extends AppBaseController
{
    private CenterRepository $centerRepository;

    public function __construct(CenterRepository $centerRepo)
    {
        $this->centerRepository = $centerRepo;
    }

    /**
     * Display a listing of the Centers.
     * GET|HEAD /centers
     */
    public function index(Request $request): JsonResponse
    {
        $centers = $this->centerRepository->scopeWithFarmerCount()->withCount('cohorts')->paginate($request->get('limit', 200));

        return $this->sendResponse($centers->toArray(), 'Centers retrieved successfully');
    }

    /**
     * Store a newly created Center in storage.
     * POST /centers
     */
    public function store(CreateCenterAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $center = $this->centerRepository->create($input);

        return $this->sendResponse($center->toArray(), 'Center saved successfully');
    }

    /**
     * Display the specified Center.
     * GET|HEAD /centers/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Center $center */
        $center = $this->centerRepository->find($id);

        if (empty($center)) {
            return $this->sendError('Center not found');
        }

        return $this->sendResponse($center->toArray(), 'Center retrieved successfully');
    }

    /**
     * Update the specified Center in storage.
     * PUT/PATCH /centers/{id}
     */
    public function update($id, UpdateCenterAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Center $center */
        $center = $this->centerRepository->find($id);

        if (empty($center)) {
            return $this->sendError('Center not found');
        }

        $center = $this->centerRepository->update($input, $id);

        return $this->sendResponse($center->toArray(), 'Center updated successfully');
    }

    /**
     * Remove the specified Center from storage.
     * DELETE /centers/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Center $center */
        $center = $this->centerRepository->find($id);

        if (empty($center)) {
            return $this->sendError('Center not found');
        }

        $center->delete();

        return $this->sendSuccess('Center deleted successfully');
    }
}
