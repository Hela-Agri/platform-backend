<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingAPIRequest;
use App\Http\Requests\API\UpdateSettingAPIRequest;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Jobs\UploadFile;
/**
 * Class SettingAPIController
 */
class SettingAPIController extends AppBaseController
{
    private SettingRepository $settingRepository;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
    }

    /**
     * Display a listing of the Settings.
     * GET|HEAD /settings
     */
    public function index(Request $request): JsonResponse
    {
        /** @var Setting $setting */
        $setting = $this->settingRepository->with('uploads')->first();

        if (empty($setting)) {
            return $this->sendError('Setting not found');
        }

        return $this->sendResponse($setting->toArray(), 'Setting retrieved successfully');
    }

    /**
     * Store a newly created Setting in storage.
     * POST /settings
     */
    public function store(CreateSettingAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $setting = $this->settingRepository->create($input);

        if($request->has('logo_file')){
            $file =$request->file('logo_file');

                if(!empty($file)){

                    $payload=array(
                        'user_id'=> $request->user()->id,
                        'file_content'=> base64_encode(file_get_contents($file)),
                        'file_name'=>$file->getClientOriginalName(),
                        'original_type'=> $file->getClientOriginalExtension(),
                        'type'=> $file->getClientMimeType(),
                        'ext'=> $file->getClientOriginalExtension(),
                        'size'=>  $file->getSize(),
                        'entity'=> $setting->id,
                        'entity_type'=> 'logo_file',
                        'delete_previous'=>false,
                    );

                     UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

                }
        }



        return $this->sendResponse($setting->toArray(), 'Setting saved successfully');
    }

    /**
     * Display the specified Setting.
     * GET|HEAD /settings/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Setting $setting */
        $setting = $this->settingRepository->find($id);

        if (empty($setting)) {
            return $this->sendError('Setting not found');
        }

        return $this->sendResponse($setting->toArray(), 'Setting retrieved successfully');
    }

    /**
     * Update the specified Setting in storage.
     * PUT/PATCH /settings/{id}
     */
    public function update($id, UpdateSettingAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Setting $setting */
        $setting = $this->settingRepository->find($id);

        if (empty($setting)) {
            return $this->sendError('Setting not found');
        }

        $setting = $this->settingRepository->update($input, $id);

        if($request->has('logo_file')){
            $file =$request->file('logo_file');

                if(!empty($file)){

                    $payload=array(
                        'user_id'=> $request->user()->id,
                        'file_content'=> base64_encode(file_get_contents($file)),
                        'file_name'=>$file->getClientOriginalName(),
                        'original_type'=> $file->getClientOriginalExtension(),
                        'type'=> $file->getClientMimeType(),
                        'ext'=> $file->getClientOriginalExtension(),
                        'size'=>  $file->getSize(),
                        'entity'=> $setting->id,
                        'entity_type'=> 'logo_file',
                        'delete_previous'=>true,
                    );

                     UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

                }
        }

        return $this->sendResponse($setting->toArray(), 'Setting updated successfully');
    }

    /**
     * Remove the specified Setting from storage.
     * DELETE /settings/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Setting $setting */
        $setting = $this->settingRepository->find($id);

        if (empty($setting)) {
            return $this->sendError('Setting not found');
        }

        $setting->delete();

        return $this->sendSuccess('Setting deleted successfully');
    }
}
