<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Exceptions\OAuthServerException as passportOAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController as ATC;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Response;

class AccessTokenController extends ATC
{
    public function issueToken(ServerRequestInterface $request)
    {
        try {
          
            $username = $request->getParsedBody()['username'];
            

            //generate token
            $tokenResponse = parent::issueToken($request);

            //convert response to json string
            $content = $tokenResponse->getContent();

            //convert json to array
            $data = json_decode($content, true);
         
            if (isset($data["error"]))
                throw new OAuthServerException('The user credentials were incorrect.', 6, 'invalid_grant', 401);

           
            $user = User::with('role')->where('username', $username)->orWhere('email', $username)->orWhere('phone_number', $username)->whereNotNull('email_verified_at')->latest()->first();

          
            $data['user']["id"] = $user->id;
            $data['user']["first_name"] = $user->first_name;
            $data['user']["middle_name"] = $user->middle_name;
            $data['user']["last_name"] = $user->last_name;
            $data['user']["username"] = $user->username;
            $data['user']["email"] = $user->email;
            $data['user']["phone_number"] = $user->phone_number;
            $data['user']["role"]['id'] = $user->role->id;
            $data['user']["role"]['name'] = $user->role->name;
            $data['user']["permissions"] = $user->role->permissions->pluck('code');
            return Response::json($data);
        } catch (ModelNotFoundException $e) { //
            //return error message
            return response(["message" => "User not found"], 500);
        } catch (passportOAuthServerException $e) {
    
            $data = [
                "error" => "invalid_grant",
                "error_description" => "The user credentials were incorrect.",
                "message" => "The user credentials were incorrect."
            ];

            //return error message
            return response($data, 401);
        } catch (Exception $e) {

            Log::info($e);
            //return error message
            return response(["message" => "Internal server error"], 401);
        }
    }
}
