<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateUserAPIRequest;
use App\Http\Requests\API\UpdateUserAPIRequest;
use Illuminate\Http\JsonResponse;
use App\Notifications\RegistrationMailNotification;
use App\Notifications\MailNotification;
class UserAPIController extends AppBaseController
{
    public $userRepository;
    public $roleUserRepository;

    public function __construct(UserRepository $userRepo, RoleRepository $roleRepo){
        $this->userRepository = $userRepo;
        $this->roleRepository = $roleRepo;
    }

    public function index(Request $request): JsonResponse
    {
        $users = $this->userRepository->with('role:id,name')
        ->whereHas('role',function($q) use ($request) {
            $q->where('code','<>','farmer');
            if ($request->has('role_type')) {
                $q->where('code', $request->get('role_type'));
            }
        })
        ->orderBy('created_at','desc')
        ->paginate(100);

        return $this->sendResponse($users->toArray(), 'users retrieved successfully');
    }

    public function store(CreateUserAPIRequest $request){
        $input = $request->all();
        try{
               DB::beginTransaction();
                if (isset($request->validator) && $request->validator->fails()) {
                    return response([
                        'status' => 'failed',
                        'errors' => $request->validator->errors()
                    ], 422);
                }
                $password=Str::random(8);
                $token = base64_encode($password);

                $input['password'] =  Hash::make( $password);
                $input['remember_token'] = Str::random(10);
                $input['verification_token'] =$token;
                $user = $this->userRepository->create($input);

                $role = Role::find($input['role_id']);

                if ($role->code == 'off_taker') {
                    Wallet::create([
                        'balance' => 0,
                        'user_id' => $user->id
                    ]);
                }


                //send an email notification
                $user->notify(new RegistrationMailNotification($user,$password));

                DB::commit();
                return $this->sendResponse($user->toArray(), 'user created successfully');
        }catch(\Exception $e){
            DB::rollBack();
            Log::critical($e);
            return $this->sendError('An error occurred, contact the administrator');
        }

    }

    /**
     * Display the specified User.
     * GET|HEAD /users/{id}
    */
    public function show($id): JsonResponse
    {
        /** @var Role $role */
        $user = $this->userRepository->with(['role'])->find($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        return $this->sendResponse($user->toArray(), 'User retrieved successfully');
    }

    public function update(UpdateUserAPIRequest $request, $id){
        $input = $request->all();

        if (isset($request->validator) && $request->validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $request->validator->errors()
            ], 422);
        }

        $user = $this->userRepository->where('id', $id)->first();

        $user->update($input);

        return $this->sendResponse($user->toArray(), 'user updated successfully');
    }

    public function destroy($id){
        DB::table('role_users')->where('user_id', $id)->delete();
        $user = $this->userRepository->where('id', $id)->delete();

        return $this->sendResponse('success','user deleted successfully');
    }
    /**
     * @param ResetPasswordRequest $request
     * @param $token
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|JsonResponse|\Illuminate\Http\Response|void
     */
    public function resetPassword(ResetPasswordRequest $request, $token){
        try{
            $request['token'] = $token;
            if (isset($request->validator) && $request->validator->fails()) {
                return response([
                    'status' => 'failed',
                    'errors' => $request->validator->errors()
                ], 422);
            }

            $user = User::where('userOTP', $token)->first();

            if ($user){
                $user->password = Hash::make(\request('password'));
                $user->update();

                return $this->sendSuccess('Password has been reset successfully');
            } else {
                return response([
                    'status' => 'failed',
                    'errors' => "Kindly check th link again to reset your password"
                ], 422);
            }
        } catch(\Exception $e) {
            Log::channel('slack')->critical(
                json_encode([
                    'Origin' => 'AUTHENTICATION',
                    'Endpoint' => '/auth/reset-password-confirmation/{token}',
                    'Error' => $e->getMessage()
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }
    }


    public function changePassword(Request $request)
     {
        $user = $request->user();

         $request->validate([
            'password' => 'required|between:2,100|confirmed',
            'password_confirmation' => 'required|between:2,100',
            'current_password' => 'required|between:2,100'], []
        );

        $input=$request->all();

        if (!Hash::check($input['current_password'], $user->password)) {
            return $this->sendError("Unable to change password. Wrong old password provided ",401);
        }
        try {

            $user->password=Hash::make($input['password']);
            $user->save();
            try{
                //send an email notification
                $subject='Password Change';
                $message='We have noticed your account password has changed. If you did not make this request. Kindly notify the Admin';

                $user->notify(new MailNotification($user,$subject,'',$message));
            }catch(\Exception $e){
                \Log::critical($e);

            }
            return $this->sendResponse($user->toArray(), 'Password changed successfully');


        } catch (\Exception $e) {
            return $this->sendError( $e);

        }

     }

    public function logout()
    {
        $accessToken = \Auth::user()->token();

        \DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();

        return response()->json(null, 204);
    }


}
