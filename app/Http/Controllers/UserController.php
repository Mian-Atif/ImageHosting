<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use App\Http\Requests\signuprequest;
use App\Http\Requests\signinrequest;
use App\Service\JwtAuth;


class UserController extends Controller
{
    function signUp(signuprequest $request)
    {
        // dd($request);
        try {
            $auth = $request->validated();
            $date = date('Y-m-d h:i:s');
            //$auth['imageName']=null;
            if(!empty($auth['profile_picture'])){
                $pos  = strpos($auth['profile_picture'], ';');
                $type = explode(':', substr($auth['profile_picture'], 0, $pos))[1];
                $ext=explode('/',$type);
                $imageName=uniqid(10).'.'.$ext[1];
                $img = preg_replace('/^data:image\/\w+;base64,/', '', $auth['profile_picture']);
                $path=storage_path('app\\public\\users').'\\'.$imageName;
                file_put_contents($path,base64_decode($img));
            }

            $result = DB::table('users')->insert([
                'name'           =>  $auth['name'],
                'email'          => $auth['email'],
                'password'       => hash::make($auth['password']),
                'age'            => $auth['age'],
                'profile_picture' => $imageName,                  //'/storage/app/public/' .$file_path,
                'created_at'     => $date,
            ]);

            if ($result) {
                // data creation for email
                $details['link'] = url('user/emailConfirmation/' . $auth['email']);
                $details['user_name'] = $auth['name'];
                $details['email'] = $auth['email'];
                //send verification mail
                dispatch(new \App\Jobs\SendEmailJob($details));
                return response()->success("user is successfully signup", 200);
            } else {
                return response()->error("opration faild", 401);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }
    function emailConfirmation($email)
    {
        try {
            $user = User::where('email', $email)->first();
            if (!empty($user['id'])) {
                if (empty($user['email_verified_at'])) {
                    $user->email_verified_at = date('Y-m-d h:i:s');
                    try {
                        $user->update();
                        return response()->success("Your Email Verified Sucessfully!!!", 200);
                    } catch (Exception $ex) {
                        return response()->error("Something Went Wrong" . $ex->getMessage(), 400);
                    }
                } else {
                    return response()->json("Already Verified", 202);
                }
            } else {
                return response()->json("Linked Expired", 404);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }


    function signIn(signinrequest $request)
    {
         try {
            $auth = $request->validated();
            $user = DB::table('users')->where('email', $auth['email'])->first();
            if(!empty($user->email)){
                if (Hash::check($auth['password'], $user->password)){
                    if (($user->remember_token == null)) {
                        $data = [
                            "id" => $user->id,
                            "name" => $user->name,
                            "email" => $user->email,
                            "age" => $user->age,
                            "profile_picture" => $user->profile_picture,
                            "image_path"=>url('/storage/users')
                        ];
                        $jwt = (new JwtAuth)->createToken($data);
                        //save token in
                        User::where('email', $user->email)
                            ->update(['remember_token' => $jwt]);

                        $outputData = [
                            "token" => $jwt,
                            "data" => $data
                        ];
                    } else {
                        return response()->error("user already login", 401);
                    }
                    return response()->success($outputData, 200);
                }else{
                    return response()->error("passwoord is invalid", 400);
                }
            } else {
                return response()->error("email is invalid", 400);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    function logOut(Request $request)
    {
        $token = $request->bearerToken();

        $token = (new JwtAuth)->decodeToken($token);
        $email = $token->data->email;
        $var = User::where('email', $email)->update(['remember_token' => ""]);
        if (isset($var)) {
            return response()->success("you are successfully logout", 200);
        } else {
            return response()->error("there is some problem in logout", 500);
        }
    }
}
