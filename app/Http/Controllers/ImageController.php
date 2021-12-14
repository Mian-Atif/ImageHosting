<?php

namespace App\Http\Controllers;

use App\Http\Requests\uploadimage;
use App\Models\Picture;
use App\Models\User;
use App\Service\JwtAuth;
use Exception;
use App\Http\Requests\deletephoto;
use App\Http\Requests\searchphoto;
use App\Http\Requests\sharedwith;



use Illuminate\Http\Request;

class ImageController extends Controller
{
    function upLoadImage(uploadimage $request)
    {
        try{
        $auth = $request->validated();
        $token = $request->bearerToken();

        $token = (new JwtAuth)->decodeToken($token);
        $id = $token->data->id;
        if (!empty($auth['photo'])) {
            $pos  = strpos($auth['photo'], ';');
            $type = explode(':', substr($auth['photo'], 0, $pos))[1];
            $ext = explode('/', $type);
            $imageName = uniqid(10) . '.' . $ext[1];
            $img = preg_replace('/^data:image\/\w+;base64,/', '', $auth['photo']);
            $path = storage_path('app\\public\\posts') . '\\' . $imageName;
            file_put_contents($path, base64_decode($img));
        }
        if ($request->privacy == null) {
            $request->privacy = 'hidden';
        }

        $user=User::find($id)->first();
        $picture=new Picture();
        $picture->privacy=$request->privacy;
        $picture->photo = $imageName;
        $result=$user->picture()->save($picture);

        if ($result) {
            return response()->success("photo upload successfully", 200);
        } else {
            return response()->error("opration faild", 401);
        }
    }catch (Exception $ex) {
        return response()->error($ex->getMessage(), 400);
    }

    }

    public function showAll(Request $request)
    {
        try {
            //$auth = $request->validated();
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            $data['path'] = url('/storage/posts');
            $data['image_name'] = Picture::where('user_id', $id)->get('photo');
            return response()->success($data, 200);
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    public function deletePhoto(deletephoto $request)
    {
        try {
            $auth = $request->validated();
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            Picture::where('user_id', $id)->where('photo', $auth['photo_name'])->delete();
            return response()->success("photo deleted successfully!", 200);
        } catch (\Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    public function searchPhoto(searchphoto $request)
    {
        try {
            $auth = $request->validated();
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            $data['path'] = url('/storage/posts');
            $data['image_name'] = Picture::where('user_id', $id)->where('photo', 'like', '%' . $auth['search'] . '%')->orwhere('created_at', 'like', '%' . $auth['search'] . '%')->get('photo');
            return response()->success($data, 200);
        } catch (\Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    public function listHiddenPhoto(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            $data['path'] = url('/storage/posts');
            $data['image_name'] = Picture::where('user_id', $id)->where('privacy', 'hidden')->get('photo');
            return response()->success($data, 200);
        } catch (\Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    public function listPrivatePhoto(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            $data['path'] = url('/storage/posts');
            $data['image_name'] = Picture::where('user_id', $id)->where('privacy', 'private')->get('photo');
            return response()->success($data, 200);
        } catch (\Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    public function listPublicPhoto(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $id = $token->data->id;
            $data['path'] = url('/storage/posts');
            $data['image_name'] = Picture::where('user_id', $id)->where('privacy', 'public')->get('photo');
            return response()->success($data, 200);
        } catch (\Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    public function sharedPhoto(sharedwith $request)
    {
        try {
            $auth = $request->validated();
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $name = $token->data->name;
            $photo=Picture::find($auth['photo_id']);
            if($photo['privacy']=='private' && $photo['privacy']=='public')
            {
                Picture::where('id', $auth['photo_id'])->update(['share_with'=>$auth['email']])->get('photo');
                $details['link'] = url('upload/checkSharedPhoto');
                $details['user_name'] = $name;
                $details['email'] = $auth['email'];
                dispatch(new \App\Jobs\SendEmailSharedPhoto($details));
                return response()->success("image shared successfully", 200);
            }else{
                return response()->success("image privcy is hidden", 200);
            }
        } catch (\Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    public function checkSharedPhoto(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $email = $token->data->email;
            $data['sharedpic_path'] = url('/storage/posts');
            $data['sharedpic_name'] = Picture::where('share_with', $email)->where('privacy', 'private')->get('photo');
            return response()->success($data, 200);
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }
}
