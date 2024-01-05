<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index(){
        $user = User::all();

        return response()-> json([
            'status' => 'SUCCESS',
            'message' => 'All Users',
            'user' => $user,
        ], 200);
    }

    public function signup(Request $request){
        $validated = $request->validate([
            "name" => 'required|string',
            "username" => ['required', 'unique:users'],
            "password" => 'required|string'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
        ]);

        if($user){
            $token = $user->createToken('myapptoken')->plainTextToken;
            $response = [
                'status' => 'SUCCESS',
                'message' => 'Account Created Successfully',
                'user' => $user,
                'token' => $token
            ];
            return response($response, 200);
        } else {
            return response()->json([
                'status' => 'FAILED',
                'message' => 'Failed to Signup'
            ], 400);
        }

        

        
    }

    public function login(Request $request){
        $validated = $request->validate([
            "username" => 'required',
            "password" => 'required'
        ]);

        $user = User::where('username', $validated['username'])->first();

        if(!$user || !Hash::check($validated['password'], $user->password)){
            return response([
                'status' => 'FAILED',
                'message' => 'Login Failed',
            ], 400);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'status' => 'SUCCESS',
            'message' => 'Login Successful',
            'user' => $user,
            'token' => $token
        ];

        return response($response, 200);
    }

    public function output(){
        $output = DB::table('workers')
            ->join('output', 'workers.name','=','output.name')
            ->select('output.*','workers.shift')
            ->orderBy('output.created_at', 'desc')
            ->get();

        return response()-> json([
            'output' => $output,
        ], 200);
    }
}
