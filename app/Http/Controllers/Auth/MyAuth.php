<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MyAuth extends Controller
{
    public function auth(Request $request)
    {
        if (Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'status' => '1',
        ])) {
            return redirect('/articles');
        }else{
            return back()->with('message','Incorrect login or password');
        }
    }
}
