<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    public function loginPage(){
        return view("login");
    }
    public function signupPage(){
        return view("signup");
    }

    public function login(Request $request){
        $validated = $request->validate([
            'email'=> 'email|required',
            'password'=>'required|min:3'
        ]);

        $user = User::where('email',$validated['email'])->first();
        if($user && Hash::check($validated['password'], $user->password)){
            Auth::login($user);
            return redirect("/")->with('success', 'Login successfull!');
        }
        return redirect('/login')->with('error', 'Wrong Credentials');
    }

    public function signup(Request $request){

        $validated = $request->validate([
            'name'=> 'required|min:3|max:255',
            'dob'=> 'required|date',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:3'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'dob'=> $validated['dob'],
            'email' => $validated['email'],
            'password' => Hash::make( $validated['password']),
            'role' => 'USER'
        ]);

        Auth::login($user);
        return redirect("/")->with('message', 'Regestration successful!');
    }

    public function logout(){
       if(Auth::check()){
        Auth::logout();
        return redirect('/login')->with('success', 'Logout successful!');
       }       
       return redirect('/');
    }

    public function facebookPage(){
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookRedirect(){
       try{
            $user = Socialite::driver('facebook')->user();
            $findUser = User::where('email', $user->email)->first();

            if(!$findUser){
                $findUser = new User();
                $findUser->email = $user->email;
                $findUser->password = Hash::make('123');
                $findUser->name = $user->name;
                $findUser->role = "USER";
                
                $findUser->save();
            }
           
            Auth::login($findUser);
            return redirect()->route('home');
            
       }catch(Exception $e){
            dd('Something went wrong!! : ' . $e->getMessage() );
       } 
    }

}
