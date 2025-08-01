<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    public static function CreateToken($userEmail, $userID, $role)
    {
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24 * 30,
            'userEmail' => $userEmail,
            'userID' => $userID,
            'role' => $role,

        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public static function VerifyToken($token)
    {
        try {
            if ($token == null) {
                return 'unauthorized';
            } else {
                $key = env('JWT_KEY');
                $decode = JWT::decode($token, new Key($key, 'HS256'));

                return $decode;
            }
        } catch (Exception $e) {
            return 'unauthorized';
        }
    }

    public static function CreateEmployeeToken($email, $employeeID, $userID)
    {
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24 * 30, // 30 days
            'employeeEmail' => $email,
            'employeeID' => $employeeID,
            'userID' => $userID, // owner's ID
            'role' => 'employee',
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public static function CreateTokenForSetPassword($userEmail)
    {
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 60 * 20,
            'userEmail' => $userEmail,
            'userID' => '0',
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}
