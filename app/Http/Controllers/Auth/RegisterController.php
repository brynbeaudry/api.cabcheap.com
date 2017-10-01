<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;

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
            'user.password' => 'required|string|min:6'
        ]);
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $v = $this->validator($request->all());

        if ($v->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }
	//massage the data
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
	//create user
        $user = User::create($input);
	//build success response
        $auth =  $user->createToken('FromRegister')->accessToken;
        $success['name'] =  $user_data =  collect($user->attributesToArray())
            ->only(['first_name', 'last_name', 'email', 'id']);

        return response()->json(array('auth' => $auth, 'user' => $user_data), 201);
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
