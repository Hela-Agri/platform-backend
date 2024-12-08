<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateActivityLogAPIRequest;
use App\Http\Requests\API\UpdateActivityLogAPIRequest;
use App\Models\ActivityLog;
use App\Repositories\ActivityLogRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class ActivityLogAPIController
 */
class ActivityLogAPIController extends AppBaseController
{
    private ActivityLogRepository $activityLogRepository;

    public function __construct(ActivityLogRepository $activityLogRepo)
    {
        $this->activityLogRepository = $activityLogRepo;
    }

    /**
     * Display a listing of the ActivityLogs.
     * GET|HEAD /activity-logs
     */
    public function index(Request $request): JsonResponse
    {
        $activityLogs = $this->activityLogRepository
            ->with('user')->orderBy('created_at', 'DESC')
            ->when($request->has('user_id') && $request->get('user_id'), function ($query) use ($request) {
                $query->where('causer_id', $request->get('user_id'));
            })
            ->paginate($request->get('limit',50));

        return $this->sendResponse($activityLogs->toArray(), 'Activity Logs retrieved successfully');
    }

    /**
     * Store a newly created ActivityLog in storage.
     * POST /activity-logs
     */
    public function store(CreateActivityLogAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $activityLog = $this->activityLogRepository->create($input);

        return $this->sendResponse($activityLog->toArray(), 'Activity Log saved successfully');
    }

    /**
     * Display the specified ActivityLog.
     * GET|HEAD /activity-logs/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var ActivityLog $activityLog */
        $activityLog = $this->activityLogRepository->find($id);

        if (empty($activityLog)) {
            return $this->sendError('Activity Log not found');
        }

        return $this->sendResponse($activityLog->toArray(), 'Activity Log retrieved successfully');
    }

    /**
     * Update the specified ActivityLog in storage.
     * PUT/PATCH /activity-logs/{id}
     */
    public function update($id, UpdateActivityLogAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var ActivityLog $activityLog */
        $activityLog = $this->activityLogRepository->find($id);

        if (empty($activityLog)) {
            return $this->sendError('Activity Log not found');
        }

        $activityLog = $this->activityLogRepository->update($input, $id);

        return $this->sendResponse($activityLog->toArray(), 'ActivityLog updated successfully');
    }

    /**
     * Remove the specified ActivityLog from storage.
     * DELETE /activity-logs/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var ActivityLog $activityLog */
        $activityLog = $this->activityLogRepository->find($id);

        if (empty($activityLog)) {
            return $this->sendError('Activity Log not found');
        }

        $activityLog->delete();

        return $this->sendSuccess('Activity Log deleted successfully');
    }
}
