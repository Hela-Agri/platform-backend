<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateModuleAPIRequest;
use App\Http\Requests\API\UpdateModuleAPIRequest;
use App\Models\Module;
use App\Repositories\ModuleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class ModuleAPIController
 */
class ModuleAPIController extends AppBaseController
{
    private ModuleRepository $moduleRepository;

    public function __construct(ModuleRepository $moduleRepo)
    {
        $this->moduleRepository = $moduleRepo;
    }

    /**
     * Display a listing of the Modules.
     * GET|HEAD /modules
     */
    public function index(Request $request): JsonResponse
    {
        $modules = $this->moduleRepository->paginate($request->get('limit', 100));

        return $this->sendResponse($modules->toArray(), 'Modules retrieved successfully');
    }

    /**
     * Store a newly created Module in storage.
     * POST /modules
     */
    public function store(CreateModuleAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $module = $this->moduleRepository->create($input);

        return $this->sendResponse($module->toArray(), 'Module saved successfully');
    }

    /**
     * Display the specified Module.
     * GET|HEAD /modules/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Module $module */
        $module = $this->moduleRepository->find($id);

        if (empty($module)) {
            return $this->sendError('Module not found');
        }

        return $this->sendResponse($module->toArray(), 'Module retrieved successfully');
    }

    /**
     * Update the specified Module in storage.
     * PUT/PATCH /modules/{id}
     */
    public function update($id, UpdateModuleAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Module $module */
        $module = $this->moduleRepository->find($id);

        if (empty($module)) {
            return $this->sendError('Module not found');
        }

        $module = $this->moduleRepository->update($input, $id);

        return $this->sendResponse($module->toArray(), 'Module updated successfully');
    }

    /**
     * Remove the specified Module from storage.
     * DELETE /modules/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Module $module */
        $module = $this->moduleRepository->find($id);

        if (empty($module)) {
            return $this->sendError('Module not found');
        }

        $module->delete();

        return $this->sendSuccess('Module deleted successfully');
    }
}
