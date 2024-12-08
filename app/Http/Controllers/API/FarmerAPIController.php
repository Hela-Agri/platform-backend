<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFarmerAPIRequest;
use App\Http\Requests\API\UpdateFarmerAPIRequest;
use App\Models\Farmer;
use App\Models\Kin;
use App\Models\Farm;
use App\Models\Role;
use App\Models\Unit;
use App\Repositories\FarmerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Jobs\UploadFile;
/**
 * Class FarmerAPIController
 */
class FarmerAPIController extends AppBaseController
{
    private FarmerRepository $farmerRepository;

    public function __construct(FarmerRepository $farmerRepo)
    {
        $this->farmerRepository = $farmerRepo;
    }
    /**
     * Display a listing of the Farmers.
     * GET|HEAD /farmers
     */
    public function index(Request $request): JsonResponse
    {
        $farmers = $this->farmerRepository
            ->whereHas('role',function($q){
                $q->where('code','farmer');
        })
        ->when($request->has('first_name'),function($query) use($request){
            return $query->where('first_name',$request->first_name);
        })
        ->when($request->has('middle_name'),function($query) use($request){
            return $query->where('middle_name','like', '%' .$request->middle_name. '%');
        })
        ->when($request->has('last_name'),function($query) use($request){
            return $query->where('last_name','like', '%' .$request->last_name. '%');
        })
        ->when($request->has('phone_number'),function($query) use($request){
            return $query->where('phone_number','like', '%' .$request->phone_number. '%');
        })
        ->when($request->has('email'),function($query) use($request){
            return $query->where('email','like', '%' .$request->email. '%');
        })
        ->when($request->has('cohort_id'),function($query) use($request){
            return $query->whereHas('farmActivities',function($query1) use($request){
                $query1->where('cohort_id', $request->get('cohort_id'));
            });
        })
        ->when($request->has('center_id'),function($query) use($request){
              $query->whereHas('farmActivities',function($query1) use($request){
                $query1->join('cohorts', 'cohorts.id', '=', 'farm_activities.cohort_id')
                ->where('cohorts.center_id', $request->get('center_id'));
              });
        })

        ->paginate($request->get('limit', 50));

        return $this->sendResponse($farmers->toArray(), 'Farmers retrieved successfully');
    }

    /**
     * Store a newly created Farmer in storage.
     * POST /farmers
     */
    public function store(CreateFarmerAPIRequest $request): JsonResponse
    {

        DB::beginTransaction();

        try{
            Schema::disableForeignKeyConstraints();


            $user_input = $request->except(['kins','farms']);


            $password=Str::random(8);
            $token = base64_encode($password);
            $user_input['role_id']=Role::where('code','farmer')->first()->id;
            $user_input['password'] =  Hash::make( $password);
            $user_input['remember_token'] = Str::random(10);
            $user_input['verification_token'] =$token;

            $farmer = $this->farmerRepository->create($user_input);

            //create kins
            if($request->has('kins')){
                $kins=json_decode($request->get('kins',[]),true);

                foreach($kins as $kin){
                    $kin['user_id']=$farmer->id;


                    $validator =  Validator::make($kin,Kin::$rules);
                    if ($validator->fails()) {
                        // Handle validation failure, such as returning errors to the api
                        return $this->sendError($validator->errors());
                    }else{
                        Kin::updateOrCreate($kin);
                    }

                }
            }

            //create farms
            if($request->has('farms')){
                $farms=json_decode($request->get('farms',[]),true);

                foreach($farms as $farm){
                    $farm['user_id']=$farmer->id;
                    $farm['acres']=$farm['size']*Unit::find($farm['unit_id'])->ratio;

                    $validator =  Validator::make($farm,Farm::$rules);
                    if ($validator->fails()) {
                        // Handle validation failure, such as returning errors to the api
                        return $this->sendError($validator->errors());
                    }else{
                        Farm::updateOrCreate($farm);
                    }

                }
            }

            if($request->has('passport_file')){
                $file =$request->file('passport_file');

                    if(!empty($file)){

                        $payload=array(
                            'user_id'=> $farmer->id,
                            'file_content'=> base64_encode(file_get_contents($file)),
                            'file_name'=>$file->getClientOriginalName(),
                            'original_type'=> $file->getClientOriginalExtension(),
                            'type'=> $file->getClientMimeType(),
                            'ext'=> $file->getClientOriginalExtension(),
                            'size'=>  $file->getSize(),
                            'entity'=> $farmer->id,
                            'entity_type'=> 'passport_file',
                            'delete_previous'=>false,
                        );

                         UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

                    }
            }
            if($request->has('id_front_file')){
                $file =$request->file('id_front_file');

                    if(!empty($file)){

                        $payload=array(
                            'user_id'=> $farmer->id,
                            'file_content'=> base64_encode(file_get_contents($file)),
                            'file_name'=>$file->getClientOriginalName(),
                            'original_type'=> $file->getClientOriginalExtension(),
                            'type'=> $file->getClientMimeType(),
                            'ext'=> $file->getClientOriginalExtension(),
                            'size'=>  $file->getSize(),
                            'entity'=> $farmer->id,
                            'entity_type'=> 'id_front_file',
                            'delete_previous'=>false,
                        );

                         UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

                    }
            }
            if($request->has('id_back_file')){
                $file =$request->file('id_back_file');

                    if(!empty($file)){

                        $payload=array(
                            'user_id'=> $farmer->id,
                            'file_content'=> base64_encode(file_get_contents($file)),
                            'file_name'=>$file->getClientOriginalName(),
                            'original_type'=> $file->getClientOriginalExtension(),
                            'type'=> $file->getClientMimeType(),
                            'ext'=> $file->getClientOriginalExtension(),
                            'size'=>  $file->getSize(),
                            'entity'=> $farmer->id,
                            'entity_type'=> 'id_back_file',
                            'delete_previous'=>false,
                        );

                         UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

                    }
            }

            DB::commit();

            Schema::enableForeignKeyConstraints();
            return $this->sendResponse($farmer->toArray(), 'Farmer saved successfully');

        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            return $this->sendError($e);
        }




    }

    /**
     * Display the specified Farmer.
     * GET|HEAD /farmers/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Farmer $farmer */
        $farmer = $this->farmerRepository
            ->with(['farms', 'kins', 'loans', 'farmActivities'])
            ->withCount('farms')
            ->withSum('loans', 'total')
            ->find($id);

        if (empty($farmer)) {
            return $this->sendError('Farmer not found');
        }

        return $this->sendResponse($farmer->toArray(), 'Farmer retrieved successfully');
    }

    /**
     * Update the specified Farmer in storage.
     * PUT/PATCH /farmers/{id}
     */
    public function update($id, UpdateFarmerAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Farmer $farmer */
        $farmer = $this->farmerRepository->find($id);

        if (empty($farmer)) {
            return $this->sendError('Farmer not found');
        }

        $farmer = $this->farmerRepository->update($input, $id);

        if($request->has('passport_file')){
            $file =$request->file('passport_file');

            if(!empty($file)){

                $payload=array(
                    'user_id'=> $farmer->id,
                    'file_content'=> base64_encode(file_get_contents($file)),
                    'file_name'=>$file->getClientOriginalName(),
                    'original_type'=> $file->getClientOriginalExtension(),
                    'type'=> $file->getClientMimeType(),
                    'ext'=> $file->getClientOriginalExtension(),
                    'size'=>  $file->getSize(),
                    'entity'=> $farmer->id,
                    'entity_type'=> 'passport_file',
                    'delete_previous'=>false,
                );

                UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

            }
        }
        if($request->has('id_front_file')){
            $file =$request->file('id_front_file');

            if(!empty($file)){

                $payload=array(
                    'user_id'=> $farmer->id,
                    'file_content'=> base64_encode(file_get_contents($file)),
                    'file_name'=>$file->getClientOriginalName(),
                    'original_type'=> $file->getClientOriginalExtension(),
                    'type'=> $file->getClientMimeType(),
                    'ext'=> $file->getClientOriginalExtension(),
                    'size'=>  $file->getSize(),
                    'entity'=> $farmer->id,
                    'entity_type'=> 'id_front_file',
                    'delete_previous'=>false,
                );

                UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

            }
        }
        if($request->has('id_back_file')){
            $file =$request->file('id_back_file');

            if(!empty($file)){

                $payload=array(
                    'user_id'=> $farmer->id,
                    'file_content'=> base64_encode(file_get_contents($file)),
                    'file_name'=>$file->getClientOriginalName(),
                    'original_type'=> $file->getClientOriginalExtension(),
                    'type'=> $file->getClientMimeType(),
                    'ext'=> $file->getClientOriginalExtension(),
                    'size'=>  $file->getSize(),
                    'entity'=> $farmer->id,
                    'entity_type'=> 'id_back_file',
                    'delete_previous'=>false,
                );

                UploadFile::dispatch($payload);//UploadFile::dispatchSync($payload);

            }
        }


        return $this->sendResponse($farmer->toArray(), 'Farmer updated successfully');
    }

    /**
     * Remove the specified Farmer from storage.
     * DELETE /farmers/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Farmer $farmer */
        $farmer = $this->farmerRepository->find($id);

        if (empty($farmer)) {
            return $this->sendError('Farmer not found');
        }
        Schema::disableForeignKeyConstraints();

        $farmer->farms()->delete();
        $farmer->farmActivities()->delete();
        $farmer->kins()->delete();
        $farmer->delete();

        Schema::enableForeignKeyConstraints();

        return $this->sendSuccess('Farmer deleted successfully');
    }
}
