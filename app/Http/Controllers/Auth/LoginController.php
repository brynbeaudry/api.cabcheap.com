<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
     //protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $token =  $user->createToken('FromLogin');
            $access_token =  $token->accessToken;
            $token = collect($token->token);
            $expires_at = $token->get('expires_at');

            $user_data =  $user->only(['first_name', 'last_name', 'email', 'id']);
            return response()->json(array('auth' => array('access_token' => $access_token, 'expires_at' => $expires_at),
                'user' => $user_data), 201);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
    
     /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
	$request->user()->token()->revoke();
	//$request->user()->token()->delete(); 
	
        return response()->json(['success' => "logged out"], $this->successStatus);	
    }	

}
