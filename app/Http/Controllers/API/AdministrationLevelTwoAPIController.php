<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAdministrationLevelTwoAPIRequest;
use App\Http\Requests\API\UpdateAdministrationLevelTwoAPIRequest;
use App\Models\AdministrationLevelTwo;
use App\Repositories\AdministrationLevelTwoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class AdministrationLevelTwoAPIController
 */
class AdministrationLevelTwoAPIController extends AppBaseController
{
    private AdministrationLevelTwoRepository $administrationLevelTwoRepository;

    public function __construct(AdministrationLevelTwoRepository $administrationLevelTwoRepo)
    {
        $this->administrationLevelTwoRepository = $administrationLevelTwoRepo;
    }

    /**
     * Display a listing of the AdministrationLevelTwos.
     * GET|HEAD /administration-level-twos
     */
    public function index(Request $request): JsonResponse
    {
        $administrationLevelTwos = $this->administrationLevelTwoRepository
        ->when($request->has('administration_level_one'),function($q) use($request){
            return $q->where('administration_level_one_id',$request->get('administration_level_one'));
        })
        ->orderBy('name', 'asc')->paginate($request->get('limit', 100));

        return $this->sendResponse($administrationLevelTwos->toArray(), 'Administration Level Twos retrieved successfully');
    }

    /**
     * Store a newly created AdministrationLevelTwo in storage.
     * POST /administration-level-twos
     */
    public function store(CreateAdministrationLevelTwoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $administrationLevelTwo = $this->administrationLevelTwoRepository->create($input);

        return $this->sendResponse($administrationLevelTwo->toArray(), 'Administration Level Two saved successfully');
    }

    /**
     * Display the specified AdministrationLevelTwo.
     * GET|HEAD /administration-level-twos/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var AdministrationLevelTwo $administrationLevelTwo */
        $administrationLevelTwo = $this->administrationLevelTwoRepository->find($id);

        if (empty($administrationLevelTwo)) {
            return $this->sendError('Administration Level Two not found');
        }

        return $this->sendResponse($administrationLevelTwo->toArray(), 'Administration Level Two retrieved successfully');
    }

    /**
     * Update the specified AdministrationLevelTwo in storage.
     * PUT/PATCH /administration-level-twos/{id}
     */
    public function update($id, UpdateAdministrationLevelTwoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var AdministrationLevelTwo $administrationLevelTwo */
        $administrationLevelTwo = $this->administrationLevelTwoRepository->find($id);

        if (empty($administrationLevelTwo)) {
            return $this->sendError('Administration Level Two not found');
        }

        $administrationLevelTwo = $this->administrationLevelTwoRepository->update($input, $id);

        return $this->sendResponse($administrationLevelTwo->toArray(), 'AdministrationLevelTwo updated successfully');
    }

    /**
     * Remove the specified AdministrationLevelTwo from storage.
     * DELETE /administration-level-twos/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var AdministrationLevelTwo $administrationLevelTwo */
        $administrationLevelTwo = $this->administrationLevelTwoRepository->find($id);

        if (empty($administrationLevelTwo)) {
            return $this->sendError('Administration Level Two not found');
        }

        $administrationLevelTwo->delete();

        return $this->sendSuccess('Administration Level Two deleted successfully');
    }
}
