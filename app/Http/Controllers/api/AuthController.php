<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ada kesalahan',
                'data' => $validator->errors()
            ]);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['name'] = $user->name;

        return response()->json([
            'success' => true,
            'message' => 'Sukses register',
            'data' => $success
        ]);
    }

    public function login(Request $request)
    {
        // cocokkan email dan password
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // jika email dan password benar
            $auth = Auth::user();
            $success["token"] = $auth->createToken('auth_token')->plainTextToken;
            $success["name"] = $auth->name;

            return response()->json([
                'success' => true,
                'message' => 'Sukses login',
                'data' => $success
            ]);
        } else {
            // jika email dan password salah
            return response()->json([
                'success' => false,
                'message' => 'Cek email dan password lagi',
                'data' => null
            ]);
        }
    }

    public function index()
    {
        $user = User::all();

        return response()->json([
            'success' => true,
            'message' => 'Sukses ambil data user',
            'data' => $user
        ]);
    }
}
