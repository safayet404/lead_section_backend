<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function UserRegistration(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|string|max:50',
                'password' => 'required|string|min:6',
                'role_id' => 'required|exists:roles,id',

            ]);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'password' => $validated['password'],
                'role_id' => $validated['role_id'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'User Registration Successfull']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);

        }
    }

public function UserLogin(Request $request)
{
    try {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->select('id', 'password', 'role_id')->first();

        if ($user && Hash::check($password, $user->password)) {
            $token = JWTToken::CreateToken($email, $user->id, $user->role_id);

            $response = response()->json([
                'status' => 'success',
                'message' => 'User Login Successful'
            ]);

            // Set cookie with explicit localhost domain
            $response->cookie(
                'token',
                $token,
                60 * 24 * 30,      // minutes
                '/',               // path
                'localhost',       // domain - explicitly set to localhost
                false,             // secure
                true,              // httpOnly
                false,             // raw
                'Lax'              // sameSite
            );

            return $response;

        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized',
            ]);
        }
    } catch (Exception $e) {
        return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
    }
}

    public function UserList(Request $request)
    {
        $list = User::all();

        return response()->json(['status' => 'success', 'list' => $list]);
    }

    public function UserLogout(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ])->cookie('token', '', -1);
    }

    public function ResetPassword(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');
            $hashedPassword = Hash::make($password);

            $userCount = User::where('email', '=', $email)->first();

            if ($userCount) {
                User::where('email', '=', $email)->update(['password' => $hashedPassword]);
            }

            return response()->json(['status' => 'success', 'message' => 'Password update successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function UserProfile(Request $request)
    {
        $email = $request->header('email');
        $user = User::where('email', $email)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Request Success',
            'data' => $user,
        ]);

    }
}
