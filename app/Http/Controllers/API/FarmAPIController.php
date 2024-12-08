<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFarmAPIRequest;
use App\Http\Requests\API\UpdateFarmAPIRequest;
use App\Models\Farm;
use App\Repositories\FarmRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Unit;
/**
 * Class FarmAPIController
 */
class FarmAPIController extends AppBaseController
{
    private FarmRepository $farmRepository;

    public function __construct(FarmRepository $farmRepo)
    {
        $this->farmRepository = $farmRepo;
    }

    /**
     * Display a listing of the Farms.
     * GET|HEAD /farms
     */
    public function index(Request $request): JsonResponse
    {
        $farms = $this->farmRepository->paginate($request->get('limit', 50));

        return $this->sendResponse($farms->toArray(), 'Farms retrieved successfully');
    }

    /**
     * Store a newly created Farm in storage.
     * POST /farms
     */
    public function store(CreateFarmAPIRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['acres']=$input['size']*Unit::find($input['unit_id'])->ratio;
        $farm = $this->farmRepository->create($input);

        return $this->sendResponse($farm->toArray(), 'Farm saved successfully');
    }

    /**
     * Display the specified Farm.
     * GET|HEAD /farms/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Farm $farm */
        $farm = $this->farmRepository->find($id);

        if (empty($farm)) {
            return $this->sendError('Farm not found');
        }

        return $this->sendResponse($farm->toArray(), 'Farm retrieved successfully');
    }

    /**
     * Update the specified Farm in storage.
     * PUT/PATCH /farms/{id}
     */
    public function update($id, UpdateFarmAPIRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['acres']=$input['size']*Unit::find($input['unit_id'])->ratio;
        /** @var Farm $farm */
        $farm = $this->farmRepository->find($id);

        if (empty($farm)) {
            return $this->sendError('Farm not found');
        }

        $farm = $this->farmRepository->update($input, $id);

        return $this->sendResponse($farm->toArray(), 'Farm updated successfully');
    }

    /**
     * Remove the specified Farm from storage.
     * DELETE /farms/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Farm $farm */
        $farm = $this->farmRepository->find($id);

        if (empty($farm)) {
            return $this->sendError('Farm not found');
        }

        $farm->delete();

        return $this->sendSuccess('Farm deleted successfully');
    }
}
