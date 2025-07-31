<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
        function UserRegistration(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|string|max:50',
                'password' => 'required|string|min:6',
                'role' => 'required|string'


            ]);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'password' => $validated['password'],
                'role' => $validated['role']
            ]);

            return response()->json(['status' => 'success', 'message' => 'User Registration Successfull']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);

        }
    }

    function UserLogin(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email' ,$email)->select('id','password','role')->first();

        if($user && Hash::check($password ,$user->password))
        {
            $token = JWTToken::CreateToken($email,$user->id,$user->role);

            return response()->json([
                'status' => 'success',
                'message' => "User Login Successfull",
                'token' => $token
            ])->cookie('token', $token, 60*24*30);
        }else {
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized'
            ]);
        }
    }

    function UserLogout(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => "Logged out successfully"
        ])->cookie('token','',-1);
    }

    function ResetPassword(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');
            $hashedPassword = Hash::make($password);

            $userCount = User::where('email','=',$email)->first();

            if($userCount)
            {
                User::where('email','=',$email)->update(['password' => $hashedPassword]);
            }
            return response()->json(['status' => 'success','message' => "Password update successfully"]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }


}
