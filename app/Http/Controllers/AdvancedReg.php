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
            'email' => 'required|max:250|email',
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
        $user = User::where('email', '=', $request->input('email'))->first();
        if (!empty($user->email)) {
            if ($user->status == '0') {
                return 'This email was registered but not submitted. Please, check your e-mail or ask for
                <a href="/repeat_confirm">Repeat link</a>';
            } else {
                return "User with current e-mail was registered. Forgot your password?";
            }
        }
    }

    public function confirm($token)
    {
        $model = ConfirmUsers::where('token', '=', $token)->firstOrFail();
        $user = User::where('email', '=', $model->Email)->first();
        $user->status = 1;
        $user->save();
        $model->delete();
        return view('auth.login');
    }

    public function getRepeat()
    {
        return view('auth.repeat');
    }

    public function postRepeat(Request $request)
    {
        $user = User::where('email', '=', $request->input('email'))->first();
        if (!empty($user->email)) {
            if ($user->status == '0') {
                $user->touch();
                $confirm = ConfirmUsers::where('email', '=', $request->input('Email'))->first();
                $confirm->touch();
                Mail::send('emails.confirm', ['token' => $confirm->token],
                    function ($u) use ($user) //���������� ������ ������������
                    {
                        $u->from('admin@site.ru');
                        $u->to($user->email);
                        $u->subject('������������� email');
                    });
                return back()->with('message',
                    'Letter to activate successfully sent to the specified address');
            } else {
                return "This email has been confirmed";
            }
        } else {
            return "No user with this email";
        }
    }
}
