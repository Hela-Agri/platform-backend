<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAdministrationLevelThreeAPIRequest;
use App\Http\Requests\API\UpdateAdministrationLevelThreeAPIRequest;
use App\Models\AdministrationLevelThree;
use App\Repositories\AdministrationLevelThreeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class AdministrationLevelThreeAPIController
 */
class AdministrationLevelThreeAPIController extends AppBaseController
{
    private AdministrationLevelThreeRepository $administrationLevelThreeRepository;

    public function __construct(AdministrationLevelThreeRepository $administrationLevelThreeRepo)
    {
        $this->administrationLevelThreeRepository = $administrationLevelThreeRepo;
    }

    /**
     * Display a listing of the AdministrationLevelThrees.
     * GET|HEAD /administration-level-threes
     */
    public function index(Request $request): JsonResponse
    {
        $administrationLevelThrees = $this->administrationLevelThreeRepository
        ->when($request->has('administration_level_two'),function($q) use($request){
            return $q->where('administration_level_two_id',$request->get('administration_level_two'));
        })
        ->orderBy('name', 'asc')
        ->paginate($request->get('limit',100));
        return $this->sendResponse($administrationLevelThrees->toArray(), 'Administration Level Threes retrieved successfully');
    }

    /**
     * Store a newly created AdministrationLevelThree in storage.
     * POST /administration-level-threes
     */
    public function store(CreateAdministrationLevelThreeAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $administrationLevelThree = $this->administrationLevelThreeRepository->create($input);

        return $this->sendResponse($administrationLevelThree->toArray(), 'Administration Level Three saved successfully');
    }

    /**
     * Display the specified AdministrationLevelThree.
     * GET|HEAD /administration-level-threes/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var AdministrationLevelThree $administrationLevelThree */
        $administrationLevelThree = $this->administrationLevelThreeRepository->find($id);

        if (empty($administrationLevelThree)) {
            return $this->sendError('Administration Level Three not found');
        }

        return $this->sendResponse($administrationLevelThree->toArray(), 'Administration Level Three retrieved successfully');
    }

    /**
     * Update the specified AdministrationLevelThree in storage.
     * PUT/PATCH /administration-level-threes/{id}
     */
    public function update($id, UpdateAdministrationLevelThreeAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var AdministrationLevelThree $administrationLevelThree */
        $administrationLevelThree = $this->administrationLevelThreeRepository->find($id);

        if (empty($administrationLevelThree)) {
            return $this->sendError('Administration Level Three not found');
        }

        $administrationLevelThree = $this->administrationLevelThreeRepository->update($input, $id);

        return $this->sendResponse($administrationLevelThree->toArray(), 'AdministrationLevelThree updated successfully');
    }

    /**
     * Remove the specified AdministrationLevelThree from storage.
     * DELETE /administration-level-threes/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var AdministrationLevelThree $administrationLevelThree */
        $administrationLevelThree = $this->administrationLevelThreeRepository->find($id);

        if (empty($administrationLevelThree)) {
            return $this->sendError('Administration Level Three not found');
        }

        $administrationLevelThree->delete();

        return $this->sendSuccess('Administration Level Three deleted successfully');
    }
}
