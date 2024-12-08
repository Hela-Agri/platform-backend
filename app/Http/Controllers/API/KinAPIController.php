<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateKinAPIRequest;
use App\Http\Requests\API\UpdateKinAPIRequest;
use App\Models\Kin;
use App\Repositories\KinRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Validator;

/**
 * Class KinAPIController
 */
class KinAPIController extends AppBaseController
{
    private KinRepository $kinRepository;

    public function __construct(KinRepository $kinRepo)
    {
        $this->kinRepository = $kinRepo;
    }

    /**
     * Display a listing of the Kin.
     * GET|HEAD /kin
     */
    public function index(Request $request): JsonResponse
    {
        $kin = $this->kinRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($kin->toArray(), 'Kin retrieved successfully');
    }

    /**
     * Store a newly created Kin in storage.
     * POST /kin
     */
    public function store(Request $request): JsonResponse
    {
        if($request->has('kins')){
            $kins=$request->get('kins','');

            foreach($kins as $kin){
                $kin['user_id']=$request->get('user_id');

                $validator =  Validator::make($kin,Kin::$rules);
                if ($validator->fails()) {
                    // Handle validation failure, such as returning errors to the api
                    return response()->json($validator->errors());
                }else{
                    Kin::updateOrCreate($kin);
                }

            }
        }
        return $this->sendSuccess('Kin(s) saved successfully');
    }

    /**
     * Display the specified Kin.
     * GET|HEAD /kin/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Kin $kin */
        $kin = $this->kinRepository->find($id);

        if (empty($kin)) {
            return $this->sendError('Kin not found');
        }

        return $this->sendResponse($kin->toArray(), 'Kin retrieved successfully');
    }

    /**
     * Update the specified Kin in storage.
     * PUT/PATCH /kin/{id}
     */
    public function update($id, UpdateKinAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Kin $kin */
        $kin = $this->kinRepository->find($id);

        if (empty($kin)) {
            return $this->sendError('Kin not found');
        }

        $kin = $this->kinRepository->update($input, $id);

        return $this->sendResponse($kin->toArray(), 'Kin updated successfully');
    }

    /**
     * Remove the specified Kin from storage.
     * DELETE /kin/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Kin $kin */
        $kin = $this->kinRepository->find($id);

        if (empty($kin)) {
            return $this->sendError('Kin not found');
        }

        $kin->delete();

        return $this->sendSuccess('Kin deleted successfully');
    }
}
