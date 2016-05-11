<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\ConfirmUsers;
use Illuminate\Support\Facades\Mail;

class AdvancedReg extends Controller
{
    public function createRoles()
    {
        $guest = new Role();
        $guest->name = 'Guest';
        $guest->save();

        $author = new Role();
        $author->name = 'Author';
        $author->save();

        $admin = new Role();
        $admin->name = 'Admin';
        $admin->save();

        $read = new Permission();
        $read->name = 'can_read';
        $read->display_name = 'Can read posts';
        $read->save();

        $edit = new Permission();
        $edit->name = 'can_edit';
        $edit->display_name = 'Can edit posts';
        $edit->save();

        $guest->attachPermission($read);
        $author->attachPermission($read, $edit);
        $admin->attachPermission($read, $edit);

        $user = User::find(39);
        $user->attachRole($admin);

    }

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
        $userId = User::find($user->id);
        $userId->attachRole('14');
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
            if ($user->status == 0) {
                $user->touch();
                $confirm = ConfirmUsers::where('Email', '=', $request->input('email'))->first();
                $confirm->touch();
                Mail::send('email.confirm', ['token' => $confirm->token],
                    function ($u) use ($user) {
                        $u->from('admin@site.ru');
                        $u->to($user->email);
                        $u->subject('Submitting E-mail');
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
