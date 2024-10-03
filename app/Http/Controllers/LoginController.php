<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view("login");
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            if (Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ])) {
                return redirect()->route('account.dashboard');
            } else {
                return redirect()->route('account.login')->with('error', 'Either email or password is incorrect.');
            }
        } else {
            return redirect()->route('account.login')
                ->withInput()
                ->withErrors($validator);
        }
    }

    public function register()
    {
        return view('register');
    }

    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'confirmed',
                'string',
                'min: 8',
                'regex:/[A-Z]/',
                'regex:/[0-9\W]/'
            ],
            'password_confirmation' => 'required',
        ], [
            'password.regex' => 'Password must contain at least 1 uppercase letter and 1 number or special character.',
        ]);

        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 'customer';
            $user->save();

            return redirect()->route('account.login')
                ->with('success', 'Registered successfully, You can login now');
        } else {
            return redirect()->route('account.register')
                ->withInput()
                ->withErrors($validator);
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }
}
