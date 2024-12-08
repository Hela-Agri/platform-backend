<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRoleAPIRequest;
use App\Http\Requests\API\UpdateRoleAPIRequest;
use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Str;
/**
 * Class RoleAPIController
 */
class RoleAPIController extends AppBaseController
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the Roles.
     * GET|HEAD /roles
     */
    public function index(Request $request): JsonResponse
    {
        $roles = $this->roleRepository->with('permissions')->whereVisible(1)->paginate($request->get('limit', 200));

        return $this->sendResponse($roles->toArray(), 'Roles retrieved successfully');
    }

    /**
     * Store a newly created Role in storage.
     * POST /roles
     */
    public function store(CreateRoleAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $role = $this->roleRepository->create($input);

         //store role permissions
       if($request->has('permissions')){
            $permissions = $request->get('permissions',[]);
            $permissions_to_sync=array();
            foreach($permissions as $permission){
                $permissions_to_sync[$permission]= [ 'id' => Str::uuid()];
            }

            return $this->sendResponse($role->toArray(), 'Role saved successfully');
        }
    }
    /**
     * Display the specified Role.
     * GET|HEAD /roles/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Role $role */
        $role = $this->roleRepository->with(['permissions'])->find($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        return $this->sendResponse($role->toArray(), 'Role retrieved successfully');
    }

    /**
     * Update the specified Role in storage.
     * PUT/PATCH /roles/{id}
     */
    public function update($id, UpdateRoleAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Role $role */
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $role = $this->roleRepository->update($input, $id);
        //update role permissions
       if($request->has('permissions')){
        $permissions = $request->get('permissions',[]);
        $permissions_to_sync=array();
        foreach($permissions as $permission){
            $permissions_to_sync[$permission]= [ 'id' => Str::uuid()];
        }
        $role->rolePermissions()->sync($permissions_to_sync);
    }


        return $this->sendResponse($role->toArray(), 'Role updated successfully');
    }

    /**
     * Remove the specified Role from storage.
     * DELETE /roles/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Role $role */
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $role->delete();

        return $this->sendSuccess('Role deleted successfully');
    }
}
