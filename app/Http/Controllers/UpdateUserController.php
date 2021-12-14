<?php

namespace App\Http\Controllers;

use App\Service\JwtAuth;
use App\Models\User;
use Exception;
use App\Http\Requests\updaterequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\uploadimage;
use App\Http\Requests\changePassword;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\uplodprofilepic;
use App\Models\Picture;



use Illuminate\Http\Request;

class UpdateUserController extends Controller
{
    function updateUser(updaterequest $request)
    {
        try {
            $token = $request->bearerToken();
            $auth = $request->validated();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            $var = User::where('id', $id)->update(['name' => $auth['name'], 'email' => $auth['email'], 'age' => $auth['age']]);
            if (isset($var)) {
                return response()->success("data successfully updated", 200);
            } else {
                return response()->error("there is some problem in updateding", 500);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    function profilePic(uplodprofilepic $request)
    {
        try {
            $auth = $request->validated();
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            $date = date('Y-m-d h:i:s');
            // $auth['imageName']=null;
            if (!empty($auth['profile_picture'])) {
                $pos  = strpos($auth['profile_picture'], ';');
                $type = explode(':', substr($auth['profile_picture'], 0, $pos))[1];
                $ext = explode('/', $type);
                $imageName = uniqid(10) . '.' . $ext[1];
                $img = preg_replace('/^data:image\/\w+;base64,/', '', $auth['profile_picture']);
                $path = storage_path('app\\api_doc\\posts') . '\\' . $imageName;
                file_put_contents($path, base64_decode($img));

                $result = User::where('id', $id)->update(['profile_picture' => $imageName, 'updated_at' => $date]);
                if ($result) {
                    return response()->success("profile image upload successfully", 200);
                } else {
                    return response()->error("opration faild", 401);
                }
            } else {
                return response()->error("picture must be in json formate", 401);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    function changePassword(changepassword $request)
    {
        try {
            $auth = $request->validated();
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $email = $token->data->email;
            $user = User::where('email', $email)->first();
            if (Hash::check($auth['old_password'], $user->password)) {
                if ($auth['new_password'] == $auth['confirm_password']) {
                    $user->password = hash::make($auth['new_password']);
                    $user->update();
                    return response()->success("password change successfully", 200);
                } else {
                    return response()->error("new password and confirm password doesn't match", 401);
                }
            } else {
                return response()->error("old password doesn't match please re-enter ur old password", 401);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    public function setPrivacy(Request $request){
        try{
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            if($request->privacy=='hidden' || $request->privacy=='public' || $request->privacy=='private' ){
                Picture::where('user_id', $id)->where('photo',$request->photo_name)->update(['privacy' => $request->privacy]);
                return response()->success("Image visibility set!",200);
            }else{
                return response()->error("Image visibility can only be hidden, public or private!",400);
            }

        }catch(\Exception $ex){
            return response()->error($ex->getMessage(),400);
        }
    }
}
