<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'isGoogleLogin' => 'boolean',
        ]);

        $isGoogleLogin = $request->boolean('isGoogleLogin');

        if ($isGoogleLogin) {
            $adminEmails = ['adminlaundry1@gmail.com', 'adminlaundry2@gmail.com'];

            if (!in_array($request->email, $adminEmails)) {
                return back()->withErrors(['email' => 'Akun ini tidak diizinkan login via Google.']);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Akun admin tidak ditemukan.']);
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect('/dashboard');
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput();
        }

        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => $request->password, // otomatis di-hash lewat cast di User model
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}