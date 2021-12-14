<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class UserAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $token=$request->bearerToken();
        $result = User::where('remember_token',$token)->first();
        //$result = $db->findOne(["remember_token"=>$token]);

        if(isset($result)){
            return $next($request);
        }else{
            return response()->error(['message'=>"token expire!"],400);
        }

    }
}
