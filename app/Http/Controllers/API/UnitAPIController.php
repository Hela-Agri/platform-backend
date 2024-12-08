<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUnitAPIRequest;
use App\Http\Requests\API\UpdateUnitAPIRequest;
use App\Models\Unit;
use App\Repositories\UnitRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class UnitAPIController
 */
class UnitAPIController extends AppBaseController
{
    private UnitRepository $unitRepository;

    public function __construct(UnitRepository $unitRepo)
    {
        $this->unitRepository = $unitRepo;
    }

    /**
     * Display a listing of the Units.
     * GET|HEAD /units
     */
    public function index(Request $request): JsonResponse
    {
        $units = $this->unitRepository
        ->when($request->has('classification'),function($query) use($request){
            return $query->where('classification',$request->get('classification'));
        })
        
        ->paginate(100);
        return $this->sendResponse($units->toArray(), 'Units retrieved successfully');
    }

    /**
     * Store a newly created Unit in storage.
     * POST /units
     */
    public function store(CreateUnitAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $unit = $this->unitRepository->create($input);

        return $this->sendResponse($unit->toArray(), 'Unit saved successfully');
    }

    /**
     * Display the specified Unit.
     * GET|HEAD /units/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->find($id);

        if (empty($unit)) {
            return $this->sendError('Unit not found');
        }

        return $this->sendResponse($unit->toArray(), 'Unit retrieved successfully');
    }

    /**
     * Update the specified Unit in storage.
     * PUT/PATCH /units/{id}
     */
    public function update($id, UpdateUnitAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Unit $unit */
        $unit = $this->unitRepository->find($id);

        if (empty($unit)) {
            return $this->sendError('Unit not found');
        }

        $unit = $this->unitRepository->update($input, $id);

        return $this->sendResponse($unit->toArray(), 'Unit updated successfully');
    }

    /**
     * Remove the specified Unit from storage.
     * DELETE /units/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->find($id);

        if (empty($unit)) {
            return $this->sendError('Unit not found');
        }

        $unit->delete();

        return $this->sendSuccess('Unit deleted successfully');
    }
}
