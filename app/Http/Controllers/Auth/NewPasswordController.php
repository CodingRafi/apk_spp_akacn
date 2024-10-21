<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NewPasswordController extends Controller
{
    public function ubahPassword(Request $request)
    {
        return view('myauth.ubahPassword', ['request' => $request]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'new_password' => 'required',
            'password_confirmation' => 'required|same:new_password',
        ]);
        
        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);


        return redirect()->route('dashboard.' . $user->getRoleNames()[0])->with('success', 'Password Berhasil direset!');
    }
}
