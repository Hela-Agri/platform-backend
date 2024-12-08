<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSiteVisitAPIRequest;
use App\Http\Requests\API\UpdateSiteVisitAPIRequest;
use App\Models\SiteVisit;
use App\Repositories\SiteVisitRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Jobs\UploadFile;
/**
 * Class SiteVisitAPIController
 */
class SiteVisitAPIController extends AppBaseController
{
    private SiteVisitRepository $siteVisitRepository;

    public function __construct(SiteVisitRepository $siteVisitRepo)
    {
        $this->siteVisitRepository = $siteVisitRepo;
    }

    /**
     * Display a listing of the SiteVisits.
     * GET|HEAD /site-visits
     */
    public function index(Request $request): JsonResponse
    {
        $siteVisits = $this->siteVisitRepository->paginate(100);
        return $this->sendResponse($siteVisits->toArray(), 'Site Visits retrieved successfully');
    }

    /**
     * Store a newly created SiteVisit in storage.
     * POST /site-visits
     */
    public function store(CreateSiteVisitAPIRequest $request): JsonResponse
    {
        \DB::beginTransaction();
        try{
            $input = $request->all();

            $input['user_id']=\Auth::user()->id;

            $siteVisit = $this->siteVisitRepository->create($input);

            if($request->has('files')){
                $files =$request->file('files');

                 foreach ($files as $file){

                    if(!empty($file)){

                        $payload=array(
                            'user_id'=> $input['user_id'],
                            'file_content'=> base64_encode(file_get_contents($file)),
                            'file_name'=>$file->getClientOriginalName(),
                            'original_type'=> $file->getClientOriginalExtension(),
                            'type'=> $file->getClientMimeType(),
                            'ext'=> $file->getClientOriginalExtension(),
                            'size'=>  $file->getSize(),
                            'entity'=> $siteVisit->id,
                            'entity_type'=> 'farm_visit',
                            'delete_previous'=>false,
                        );
                        UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

                    }
                }

            }
            \DB::commit();
            return $this->sendResponse($siteVisit->toArray(), 'Site Visit saved successfully');
        }catch(\Exception $e){
            \DB::rollBack();
            \Log::critical($e);
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Display the specified SiteVisit.
     * GET|HEAD /site-visits/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var SiteVisit $siteVisit */
        $siteVisit = $this->siteVisitRepository->find($id);

        if (empty($siteVisit)) {
            return $this->sendError('Site Visit not found');
        }

        return $this->sendResponse($siteVisit->toArray(), 'Site Visit retrieved successfully');
    }

    /**
     * Update the specified SiteVisit in storage.
     * PUT/PATCH /site-visits/{id}
     */
    public function update($id, UpdateSiteVisitAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var SiteVisit $siteVisit */
        $siteVisit = $this->siteVisitRepository->find($id);

        if (empty($siteVisit)) {
            return $this->sendError('Site Visit not found');
        }

        $siteVisit = $this->siteVisitRepository->update($input, $id);

        return $this->sendResponse($siteVisit->toArray(), 'SiteVisit updated successfully');
    }

    /**
     * Remove the specified SiteVisit from storage.
     * DELETE /site-visits/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var SiteVisit $siteVisit */
        $siteVisit = $this->siteVisitRepository->find($id);

        if (empty($siteVisit)) {
            return $this->sendError('Site Visit not found');
        }

        $siteVisit->delete();

        return $this->sendSuccess('Site Visit deleted successfully');
    }
}
