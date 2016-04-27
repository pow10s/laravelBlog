<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\ConfirmUsers;
use Illuminate\Support\Facades\Mail;

class AdvancedReg extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|min:6|max:100',
            'email' => 'required|unique:users|max:250|unique:confirm_users|email',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        if ($user) {
            $email = $user->email;
            $token = str_random(32);
            $model = new ConfirmUsers();
            $model->email = $email;
            $model->token = $token;
            $model->save();

            Mail::send('email.confirm', ['token' => $token], function ($u) use ($user) {
                $u->from('stosdima@gmail.com');
                $u->to($user->email);
                $u->subject('Confirm registration');
            });
            return back()->with('message',
                'Allright.Please confirm: <a href="/register/confirm/' . $token . '">Link</a>');
        } else {
            return back()->withErrors('message', 'Something going wrong');
        }
    }

    public function confirm($token)
    {
        $model = ConfirmUsers::where('token', '=', $token)->firstOrFail();
        $user = User::where('email', '=', $model->Email)->first();
        $user->status = 1;
        $user->save();
        $model->delete();
        return 'Registration was finished';
    }
}
