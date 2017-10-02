<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    //protected $redirectTo = '/home';
     
    public $successStatus = 200;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:6'
        ]);
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        //dd($request->user);
        $v = $this->validator($request->user);


        if ($v->fails()) {
            return response()->json(['error'=>$v->errors()], 401);
        }
	//massage the data
        $input = $request->user;
        //dd('input', $input);
        $input['password'] = bcrypt($input['password']);
        //dd('input', $input);
	//create user
        $user = User::create($input);
        User::destroy($user->id);

	//build success response
        //dd($user->createToken('FromRegister'));
        $token = $user->createToken('FromRegister');
        $access_token =  $token->accessToken;
        //->attributes->expires_at
        $token = collect($token->token);
        $expires_at = $token->get('expires_at');
        //dd($access_token, $token->get('expires_at'));
        //dd($token
        $user_data =  $user->only(['first_name', 'last_name', 'email', 'id']);

        return response()->json(array('auth' => array('access_token' => $access_token, 'expires_at' => $expires_at), 'user' => $user_data), 201);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
