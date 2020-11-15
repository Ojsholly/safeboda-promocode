<?php

namespace App\Http\Controllers\API\v1\Admin\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\AdminRegistrationRequest;

class AuthController extends Controller
{
    //
    public function register(AdminRegistrationRequest $request)
    {
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $password = $request->password;

        $new_admin = Admin::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => Hash::make($request->password)
        ]);

        if ($new_admin == false) {
            # code...

            $response = [
                'status' => 'error',
                'message' => 'Error creating admin account.'
            ];

            return response()->json($response, 400);
        }

        $response = [
            'status' => 'success',
            'message' => 'New Admin Account Successfully Created'
        ];

        return response()->json($response, 201);

    }

    public function login(AdminLoginRequest $request)
    {

        $admin = Admin::where('email', $request->email)->first();

            if (! $admin || ! Hash::check($request->password, $admin->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $token = $admin->createToken('admin')->plainTextToken;

            $response = [
                'status' => 'success',
                'admin' => $admin,
                'token' => $token
            ];

        return response()->json($response, 200);
    }
}