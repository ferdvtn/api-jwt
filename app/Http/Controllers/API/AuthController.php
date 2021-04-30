<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('auth', ['except' => ['store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:255',
            'email' => 'required|min:3|max:100|email|unique:users,email',
            'password' => 'required|min:3|max:255|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->all(), 'Validation Errors', 422);
        }

        $User = User::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $body = [
            'user' => $User
        ];

        return $this->responseOk($body, 'Successed to create user');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $cridentials = $request->only(['email', 'password']);
        // default time to life is 60 minutes
        if ( ! $User = Auth::setTTL(60)->attempt($cridentials)) {
            return $this->responseError([], 'Cridential invalid !', 401);
        }

        $body = [
            'token' => $this->tokenInfo($User)
        ];

        return $this->responseOk($body, 'Get login succeed !');
    }

    public function show()
    {
        $body = [
            'user' => Auth::user()
        ];

        return $this->responseOk($body, 'Get user succeed !');
    }

    public function refresh()
    {
        $body = [
            // Pass true as the first param to force the token to be blacklisted "forever".
            // The second parameter will reset the claims for the new token
            'token' => Auth::refresh(true, true)
        ];

        return $this->responseOk($body, 'Get user succeed !');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'min:3|max:255',
            'email' => 'min:3|max:100|email|unique:users,email,' . Auth::id(),
            'password' => 'min:3|max:255|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->all(), 'Validation Errors', 422);
        }

        $input = $request->only(['name', 'email', 'password']);
        
        $user = Auth::user();
        foreach ($input as $field => $value) {
            if ($field == 'password') $value = Hash::make($value);
            $user->{$field} = $value;
        }
        
        $user->save();
        
        $body = [
            'user' => $user
        ];
        
        return $this->responseOk($body, 'Successed to update user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout ()
    {
        return $this->responseOk(Auth::logout(), 'Logout has been succeed !');
    }

    public function tokenInfo($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL()
        ];
    }
}
