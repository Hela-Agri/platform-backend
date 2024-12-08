<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRolePermissionAPIRequest;
use App\Http\Requests\API\UpdateRolePermissionAPIRequest;
use App\Models\RolePermission;
use App\Repositories\RolePermissionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class RolePermissionAPIController
 */
class RolePermissionAPIController extends AppBaseController
{
    private RolePermissionRepository $rolePermissionRepository;

    public function __construct(RolePermissionRepository $rolePermissionRepo)
    {
        $this->rolePermissionRepository = $rolePermissionRepo;
    }

    /**
     * Display a listing of the RolePermissions.
     * GET|HEAD /role-permissions
     */
    public function index(Request $request): JsonResponse
    {
        $rolePermissions = $this->rolePermissionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($rolePermissions->toArray(), 'Role Permissions retrieved successfully');
    }

    /**
     * Store a newly created RolePermission in storage.
     * POST /role-permissions
     */
    public function store(CreateRolePermissionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $rolePermission = $this->rolePermissionRepository->create($input);

        return $this->sendResponse($rolePermission->toArray(), 'Role Permission saved successfully');
    }

    /**
     * Display the specified RolePermission.
     * GET|HEAD /role-permissions/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var RolePermission $rolePermission */
        $rolePermission = $this->rolePermissionRepository->find($id);

        if (empty($rolePermission)) {
            return $this->sendError('Role Permission not found');
        }

        return $this->sendResponse($rolePermission->toArray(), 'Role Permission retrieved successfully');
    }

    /**
     * Update the specified RolePermission in storage.
     * PUT/PATCH /role-permissions/{id}
     */
    public function update($id, UpdateRolePermissionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var RolePermission $rolePermission */
        $rolePermission = $this->rolePermissionRepository->find($id);

        if (empty($rolePermission)) {
            return $this->sendError('Role Permission not found');
        }

        $rolePermission = $this->rolePermissionRepository->update($input, $id);

        return $this->sendResponse($rolePermission->toArray(), 'RolePermission updated successfully');
    }

    /**
     * Remove the specified RolePermission from storage.
     * DELETE /role-permissions/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var RolePermission $rolePermission */
        $rolePermission = $this->rolePermissionRepository->find($id);

        if (empty($rolePermission)) {
            return $this->sendError('Role Permission not found');
        }

        $rolePermission->delete();

        return $this->sendSuccess('Role Permission deleted successfully');
    }
}
