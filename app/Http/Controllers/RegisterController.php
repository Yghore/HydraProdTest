<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\NewRegister;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
    public function index()
    {
        return view('register');
    }
    
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest');
    // }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ],
        [
            'name' => 'Votre pseudonyme n\'est pas valide',
            'email' => 'Votre email n\'est pas valide',
            'password' => 'Votre mot de passe n\'est pas valide',
        ]
        )->validate();
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }


    public function validateAndCreate(request $request)
    {
        $data = $request->all();
        $validate = $this->validator($data);
        $user = $this->create($data);
        Mail::to($request->input('email'))->send(new NewRegister($data['name']));
        return redirect(route('users_list'))->with(['success' => 'Vous avez bien crée l\'utilisateur']);
    }



    public function deleteUser(int $id)
    {
        $userExist = DB::table('users')->where('id', '=', $id)->first();
        if(!empty($userExist))
        {
            DB::table('users')->where('id', '=', $id)->delete();
            $with = [
                'success' => 'L\'utilisateur à été supprimé !',
            ];                              
            return redirect(route('users_list'))->with($with);
        }
        return redirect(route('users_list'))->withErrors('L\'utilisateur n\'existe pas !');
    }

}