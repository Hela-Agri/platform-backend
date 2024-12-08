<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStatusAPIRequest;
use App\Http\Requests\API\UpdateStatusAPIRequest;
use App\Models\Status;
use App\Repositories\StatusRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class StatusAPIController
 */
class StatusAPIController extends AppBaseController
{
    private StatusRepository $statusRepository;

    public function __construct(StatusRepository $statusRepo)
    {
        $this->statusRepository = $statusRepo;
    }

    /**
     * Display a listing of the Statuses.
     * GET|HEAD /statuses
     */
    public function index(Request $request): JsonResponse
    {
        $statuses = $this->statusRepository->paginate($request->get('limit', 100));

        return $this->sendResponse($statuses->toArray(), 'Statuses retrieved successfully');
    }

    /**
     * Store a newly created Status in storage.
     * POST /statuses
     */
    public function store(CreateStatusAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $status = $this->statusRepository->create($input);

        return $this->sendResponse($status->toArray(), 'Status saved successfully');
    }

    /**
     * Display the specified Status.
     * GET|HEAD /statuses/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Status $status */
        $status = $this->statusRepository->find($id);

        if (empty($status)) {
            return $this->sendError('Status not found');
        }

        return $this->sendResponse($status->toArray(), 'Status retrieved successfully');
    }

    /**
     * Update the specified Status in storage.
     * PUT/PATCH /statuses/{id}
     */
    public function update($id, UpdateStatusAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Status $status */
        $status = $this->statusRepository->find($id);

        if (empty($status)) {
            return $this->sendError('Status not found');
        }

        $status = $this->statusRepository->update($input, $id);

        return $this->sendResponse($status->toArray(), 'Status updated successfully');
    }

    /**
     * Remove the specified Status from storage.
     * DELETE /statuses/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Status $status */
        $status = $this->statusRepository->find($id);

        if (empty($status)) {
            return $this->sendError('Status not found');
        }

        $status->delete();

        return $this->sendSuccess('Status deleted successfully');
    }
}
