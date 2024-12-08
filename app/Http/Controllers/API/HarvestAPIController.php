<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHarvestAPIRequest;
use App\Http\Requests\API\UpdateHarvestAPIRequest;
use App\Models\Harvest;
use App\Repositories\HarvestRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class HarvestAPIController
 */
class HarvestAPIController extends AppBaseController
{
    private HarvestRepository $harvestRepository;

    public function __construct(HarvestRepository $harvestRepo)
    {
        $this->harvestRepository = $harvestRepo;
    }

    /**
     * Display a listing of the Harvests.
     * GET|HEAD /harvests
     */
    public function index(Request $request): JsonResponse
    {
        $harvests = $this->harvestRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($harvests->toArray(), 'Harvests retrieved successfully');
    }

    /**
     * Store a newly created Harvest in storage.
     * POST /harvests
     */
    public function store(CreateHarvestAPIRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['user_id']=\Auth::user()->id;
        $harvest = $this->harvestRepository->create($input);

        return $this->sendResponse($harvest->toArray(), 'Harvest saved successfully');
    }

    /**
     * Display the specified Harvest.
     * GET|HEAD /harvests/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Harvest $harvest */
        $harvest = $this->harvestRepository->find($id);

        if (empty($harvest)) {
            return $this->sendError('Harvest not found');
        }

        return $this->sendResponse($harvest->toArray(), 'Harvest retrieved successfully');
    }

    /**
     * Update the specified Harvest in storage.
     * PUT/PATCH /harvests/{id}
     */
    public function update($id, UpdateHarvestAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Harvest $harvest */
        $harvest = $this->harvestRepository->find($id);

        if (empty($harvest)) {
            return $this->sendError('Harvest not found');
        }

        $harvest = $this->harvestRepository->update($input, $id);

        return $this->sendResponse($harvest->toArray(), 'Harvest updated successfully');
    }

    /**
     * Remove the specified Harvest from storage.
     * DELETE /harvests/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Harvest $harvest */
        $harvest = $this->harvestRepository->find($id);

        if (empty($harvest)) {
            return $this->sendError('Harvest not found');
        }

        $harvest->delete();

        return $this->sendSuccess('Harvest deleted successfully');
    }
}
