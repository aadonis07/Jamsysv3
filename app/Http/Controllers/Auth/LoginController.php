<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'username'   => 'required|max:50',
            'password' => 'required|min:6'
        ]);
        // Attempt to log the user in
        if(Auth::guard()->attempt(
            [
               'username' => $request->username,
               'password' => $request->password
            ],
            $request->remember)) {
            /**
                check if is_secured column is true or false
                true = validate ip_address and mac address
                false = authenticated
             **/
            return redirect()->intended(route('home'));
        }
        // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('username', 'remember'))->withErrors(array('username' => 'Login Authentication Failed','password' => ' '));
    }
 }
