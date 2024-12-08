<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAdministrationLevelOneAPIRequest;
use App\Http\Requests\API\UpdateAdministrationLevelOneAPIRequest;
use App\Models\AdministrationLevelOne;
use App\Repositories\AdministrationLevelOneRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class AdministrationLevelOneAPIController
 */
class AdministrationLevelOneAPIController extends AppBaseController
{
    private AdministrationLevelOneRepository $administrationLevelOneRepository;

    public function __construct(AdministrationLevelOneRepository $administrationLevelOneRepo)
    {
        $this->administrationLevelOneRepository = $administrationLevelOneRepo;
    }

    /**
     * Display a listing of the AdministrationLevelOnes.
     * GET|HEAD /administration-level-ones
     */
    public function index(Request $request): JsonResponse
    {
        $administrationLevelOnes = $this->administrationLevelOneRepository
        ->when($request->has('country_id'),function($q) use($request){
            return $q->where('country_id',$request->get('country_id'));
        })
        ->orderBy('code', 'asc')->paginate($request->get('limit', 200));

        return $this->sendResponse($administrationLevelOnes->toArray(), 'Administration Level Ones retrieved successfully');
    }

    /**
     * Store a newly created AdministrationLevelOne in storage.
     * POST /administration-level-ones
     */
    public function store(CreateAdministrationLevelOneAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $administrationLevelOne = $this->administrationLevelOneRepository->create($input);

        return $this->sendResponse($administrationLevelOne->toArray(), 'Administration Level One saved successfully');
    }

    /**
     * Display the specified AdministrationLevelOne.
     * GET|HEAD /administration-level-ones/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var AdministrationLevelOne $administrationLevelOne */
        $administrationLevelOne = $this->administrationLevelOneRepository->find($id);

        if (empty($administrationLevelOne)) {
            return $this->sendError('Administration Level One not found');
        }

        return $this->sendResponse($administrationLevelOne->toArray(), 'Administration Level One retrieved successfully');
    }

    /**
     * Update the specified AdministrationLevelOne in storage.
     * PUT/PATCH /administration-level-ones/{id}
     */
    public function update($id, UpdateAdministrationLevelOneAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var AdministrationLevelOne $administrationLevelOne */
        $administrationLevelOne = $this->administrationLevelOneRepository->find($id);

        if (empty($administrationLevelOne)) {
            return $this->sendError('Administration Level One not found');
        }

        $administrationLevelOne = $this->administrationLevelOneRepository->update($input, $id);

        return $this->sendResponse($administrationLevelOne->toArray(), 'AdministrationLevelOne updated successfully');
    }

    /**
     * Remove the specified AdministrationLevelOne from storage.
     * DELETE /administration-level-ones/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var AdministrationLevelOne $administrationLevelOne */
        $administrationLevelOne = $this->administrationLevelOneRepository->find($id);

        if (empty($administrationLevelOne)) {
            return $this->sendError('Administration Level One not found');
        }

        $administrationLevelOne->delete();

        return $this->sendSuccess('Administration Level One deleted successfully');
    }
}
