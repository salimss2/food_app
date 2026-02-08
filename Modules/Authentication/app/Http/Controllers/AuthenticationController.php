<?php

namespace Modules\Authentication\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Auth Module Works!']);
    }

    public function register(Request $request) 
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:customer,delivery,restaurant',
            'phone' => 'required'
        ]);

        // منطق التسجيل سيتم وضعه هنا
        return response()->json(['message' => 'User registered successfully']);
    }

    // أضف هذه الدوال فوراً لأن ملف api.php يحتاجها
    public function login(Request $request) 
    {
        return response()->json(['message' => 'Login method']);
    }

    public function completeDeliveryProfile(Request $request) 
    {
        return response()->json(['message' => 'Profile completion method']);
    }
}