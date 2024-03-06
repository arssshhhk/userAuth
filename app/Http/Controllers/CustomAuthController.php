<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Session;

class CustomAuthController extends Controller
{
    public function login(){
        return view("auth.login");
    }
    public function registration(){
        return view("auth.registration");
    }
    public function registerUser(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'number'=>'required|min:10|max:10|unique:users',
            'age'=>'required',
            'password'=>'required|min:8|max:16'
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->age = $request->age;
        $user->password = Hash::make($request->password);
        $res = $user->save();
        if($res){
            return back()->with('success','You have registered successfuly');
        }else{
            return back()->with('fail', 'Something wrong');
        }
    }
    public function loginUser(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:8|max:16'
        ]);
        $user = User::where('email', '=',$request->email)->first();
        if($user){
            if(Hash::check($request->password,$user->password)){
                $request->session()->put('loginId', $user->id);
                return redirect('dashboard');
            }else{
                return back()->with('fail','Password not matches.');
            }
        }else{
            return back()->with('fail','This email is not registered.');
        }
    }
    public function dashboard(){
        if(Session::has('loginId')){
            $user = User::all()->toArray(); 
        }
        return view('dashboard', compact('user'));
    }
    public function logout(){
        if(Session::has('loginId')) {
            Session::pull('loginId');
            return redirect('login');
        }
    }
}
