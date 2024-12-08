<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRelationshipAPIRequest;
use App\Http\Requests\API\UpdateRelationshipAPIRequest;
use App\Models\Relationship;
use App\Repositories\RelationshipRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class RelationshipAPIController
 */
class RelationshipAPIController extends AppBaseController
{
    private RelationshipRepository $relationshipRepository;

    public function __construct(RelationshipRepository $relationshipRepo)
    {
        $this->relationshipRepository = $relationshipRepo;
    }

    /**
     * Display a listing of the Relationships.
     * GET|HEAD /relationships
     */
    public function index(Request $request): JsonResponse
    {
        $relationships = $this->relationshipRepository->paginate(100);

        return $this->sendResponse($relationships->toArray(), 'Relationships retrieved successfully');
    }

    /**
     * Store a newly created Relationship in storage.
     * POST /relationships
     */
    public function store(CreateRelationshipAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $relationship = $this->relationshipRepository->create($input);

        return $this->sendResponse($relationship->toArray(), 'Relationship saved successfully');
    }

    /**
     * Display the specified Relationship.
     * GET|HEAD /relationships/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Relationship $relationship */
        $relationship = $this->relationshipRepository->find($id);

        if (empty($relationship)) {
            return $this->sendError('Relationship not found');
        }

        return $this->sendResponse($relationship->toArray(), 'Relationship retrieved successfully');
    }

    /**
     * Update the specified Relationship in storage.
     * PUT/PATCH /relationships/{id}
     */
    public function update($id, UpdateRelationshipAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Relationship $relationship */
        $relationship = $this->relationshipRepository->find($id);

        if (empty($relationship)) {
            return $this->sendError('Relationship not found');
        }

        $relationship = $this->relationshipRepository->update($input, $id);

        return $this->sendResponse($relationship->toArray(), 'Relationship updated successfully');
    }

    /**
     * Remove the specified Relationship from storage.
     * DELETE /relationships/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Relationship $relationship */
        $relationship = $this->relationshipRepository->find($id);

        if (empty($relationship)) {
            return $this->sendError('Relationship not found');
        }

        $relationship->delete();

        return $this->sendSuccess('Relationship deleted successfully');
    }
}
