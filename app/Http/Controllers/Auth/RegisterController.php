<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Mail\ActivateAccount;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Profile;

class RegisterController extends Controller {
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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data) {
        
        
        $newUser = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
                    'status'   => 0 
        ]);
        
        // Send user message for activation account.
        Mail::to($newUser)->send(new ActivateAccount($newUser));
        
        return $newUser;
    }

    /**
     * Make user activation.
     */
    public function activation($userId, $token) {
        $user = User::findOrFail($userId);

        // Check token in user DB. if null then check data (user make first activation).
        if (is_null($user->remember_token)) {
            // Check token from url.
            if (md5($user->email) == $token) {
                // Change status and login user.
                $user->status = 1;
                $user->save();

                \Session::flash('flash_message', trans('interface.ActivatedSuccess'));

                //create profile
                $user->profile()->save(new Profile);
                
                // Make login user.
                Auth::login($user, true);
            } else {
                // Wrong token.
                \Session::flash('flash_message_error', trans('interface.ActivatedWrong'));
            }
        } else {
            // User was activated early.
            \Session::flash('flash_message_error', trans('interface.ActivatedAlready'));
        }
        return redirect('/home');
    }

}
