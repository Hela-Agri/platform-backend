<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateActionAPIRequest;
use App\Http\Requests\API\UpdateActionAPIRequest;
use App\Models\Action;
use App\Repositories\ActionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class ActionAPIController
 */
class ActionAPIController extends AppBaseController
{
    private ActionRepository $actionRepository;

    public function __construct(ActionRepository $actionRepo)
    {
        $this->actionRepository = $actionRepo;
    }

    /**
     * Display a listing of the Actions.
     * GET|HEAD /actions
     */
    public function index(Request $request): JsonResponse
    {
        $actions = $this->actionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($actions->toArray(), 'Actions retrieved successfully');
    }

    /**
     * Store a newly created Action in storage.
     * POST /actions
     */
    public function store(CreateActionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $action = $this->actionRepository->create($input);

        return $this->sendResponse($action->toArray(), 'Action saved successfully');
    }

    /**
     * Display the specified Action.
     * GET|HEAD /actions/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Action $action */
        $action = $this->actionRepository->find($id);

        if (empty($action)) {
            return $this->sendError('Action not found');
        }

        return $this->sendResponse($action->toArray(), 'Action retrieved successfully');
    }

    /**
     * Update the specified Action in storage.
     * PUT/PATCH /actions/{id}
     */
    public function update($id, UpdateActionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Action $action */
        $action = $this->actionRepository->find($id);

        if (empty($action)) {
            return $this->sendError('Action not found');
        }

        $action = $this->actionRepository->update($input, $id);

        return $this->sendResponse($action->toArray(), 'Action updated successfully');
    }

    /**
     * Remove the specified Action from storage.
     * DELETE /actions/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Action $action */
        $action = $this->actionRepository->find($id);

        if (empty($action)) {
            return $this->sendError('Action not found');
        }

        $action->delete();

        return $this->sendSuccess('Action deleted successfully');
    }
}
