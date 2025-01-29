<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validated = $request->validate([
            'email' => 'required|exists:users,email|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (Hash::check($validated['password'], $user->password)) {
            Auth::login($user);

            return redirect()->route('dashboard');
        }

        return redirect()->route('login')->withErrors(['password' => 'Invalid password']);
    }

    public function register(Request $request) {
        $validated = $request->validate([
            'email' => 'required|unique:users|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        $user = new User();
        $user->email = $validated['email'];
        $user->password = $validated['password'];
        $user->name = str_split($user->email, strpos($user->email, '@'))[0];
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('login');
    }

    public function logout() {
        if (Auth::check()) {
            Auth::logout();
            return redirect()->route('login');
        }
    }
}
