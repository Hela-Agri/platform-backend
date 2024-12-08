<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFarmActivityItemAPIRequest;
use App\Http\Requests\API\UpdateFarmActivityItemAPIRequest;
use App\Models\FarmActivityItem;
use App\Repositories\FarmActivityItemRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class FarmActivityItemAPIController
 */
class FarmActivityItemAPIController extends AppBaseController
{
    private FarmActivityItemRepository $farmActivityItemRepository;

    public function __construct(FarmActivityItemRepository $farmActivityItemRepo)
    {
        $this->farmActivityItemRepository = $farmActivityItemRepo;
    }

    /**
     * Display a listing of the FarmActivityItems.
     * GET|HEAD /farm-activity-items
     */
    public function index(Request $request): JsonResponse
    {
        $farmActivityItems = $this->farmActivityItemRepository->paginate($request->get('limit', 50));

        return $this->sendResponse($farmActivityItems->toArray(), 'Farm Activity Items retrieved successfully');
    }

    /**
     * Store a newly created FarmActivityItem in storage.
     * POST /farm-activity-items
     */
    public function store(CreateFarmActivityItemAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $farmActivityItem = $this->farmActivityItemRepository->create($input);

        return $this->sendResponse($farmActivityItem->toArray(), 'Farm Activity Item saved successfully');
    }

    /**
     * Display the specified FarmActivityItem.
     * GET|HEAD /farm-activity-items/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var FarmActivityItem $farmActivityItem */
        $farmActivityItem = $this->farmActivityItemRepository->find($id);

        if (empty($farmActivityItem)) {
            return $this->sendError('Farm Activity Item not found');
        }

        return $this->sendResponse($farmActivityItem->toArray(), 'Farm Activity Item retrieved successfully');
    }

    /**
     * Update the specified FarmActivityItem in storage.
     * PUT/PATCH /farm-activity-items/{id}
     */
    public function update($id, UpdateFarmActivityItemAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var FarmActivityItem $farmActivityItem */
        $farmActivityItem = $this->farmActivityItemRepository->find($id);

        if (empty($farmActivityItem)) {
            return $this->sendError('Farm Activity Item not found');
        }

        $farmActivityItem = $this->farmActivityItemRepository->update($input, $id);

        return $this->sendResponse($farmActivityItem->toArray(), 'FarmActivityItem updated successfully');
    }

    /**
     * Remove the specified FarmActivityItem from storage.
     * DELETE /farm-activity-items/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var FarmActivityItem $farmActivityItem */
        $farmActivityItem = $this->farmActivityItemRepository->find($id);

        if (empty($farmActivityItem)) {
            return $this->sendError('Farm Activity Item not found');
        }

        $farmActivityItem->delete();

        return $this->sendSuccess('Farm Activity Item deleted successfully');
    }
}
