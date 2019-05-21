<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\UserResource;
use App\Jobs\Api\SaveLastTokenJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends ApiController
{
    public function index()
    {
        $users = User::paginate(3);
        return UserResource::collection($users);
    }

    public function show(Request $request)
    {
        SaveLastTokenJob::dispatch($request->user(),'job just pp');
        return $this->success(new UserResource($request->user()));
    }



    //用户注册
    public function store(UserRequest $request){
        User::create($request->all());
        return $this->setStatusCode(201)->success('用户注册成功');
    }
    //用户登录
    public function login(Request $request){
        $credentials = request(['name', 'password']);
        $res=Auth::guard('web')->attempt($credentials);

        if(!$res)
            return response()->json([
                'message' => '未验证'
            ], 401);

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        SaveLastTokenJob::dispatch($user,'job just pp');
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
//        if($res){
//            return $this->setStatusCode(201)->success('用户登录成功...');
//        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

}
