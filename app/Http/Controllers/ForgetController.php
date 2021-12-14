<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use App\Http\Requests\forgetrequest;

class ForgetController extends Controller
{
    function forgetPassword(forgetrequest $request)
    {

        try {
            $auth = $request->validated();
            //dd($auth);
            $email = $auth['email'];
            $user = User::where('email', $email)->first();

            if (isset($user)) {
                $new_password = random_int(100000,200000);
                $pass = hash::make($new_password);
                // data creation for email
                $details['new_password'] = $new_password;
                $details['user_name'] = $user['name'];
                $details['email'] = $user['email'];

                //send verification mail
                $done = dispatch(new \App\Jobs\SendEmailForgetPassword($details));
                if (isset($done)) {
                    $user->password = $pass;
                    $user->update();
                    return response()->success("successfully change please visit  your email", 200);
                } else {
                    return response()->success("there is some server error password not change", 401);
                }
            } else {
                return response()->error("user doesn't exist", 404);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }
}
