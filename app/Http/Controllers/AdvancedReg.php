<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\ConfirmUsers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $author->attachPermission($read);
        $author->attachPermission($edit);
        $admin->attachPermission($read);
        $admin->attachPermission($edit);
        /*
                $user = User::find(39);
                $user->attachRole($admin);*/

    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:6|max:100',
            'email' => 'required|max:250|email',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::where('email', '=', $request->input('email'))->first();
        if (!empty($user->email)) {
            if ($user->status == config('const.USER_STATUS_NOT_ACTIVATED')) {
                return view('errors.repeat');
            } else {
                return Redirect::back()->withErrors(['msg' => "User with current e-mail was registered. Forgot your password?"]);
            }
        } else {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]);
        }
        if ($user) {
            $email = $user->email;
            $token = str_random(32);
            $model = new ConfirmUsers();
            $model->email = $email;
            $model->token = $token;
            $model->save();

            Mail::send('email.confirm', ['token' => $token], function ($u) use ($user) {
                $u->from(config('const.EMAIL_ADMIN'));
                $u->to($user->email);
                $u->subject('Confirm registration');
            });
            return Redirect::back()->withErrors(['msg' => 'Allright.Please confirm letter sended into your e-mail']);
        } else {
            return Redirect::back()->withErrors(['msg' => "Something going wrong"]);
        }
    }

    public function confirm($token)
    {

        $model = ConfirmUsers::where('token', '=', $token)->first();
        if(isset($model->token)) {
            $user = User::where('email', '=', $model->Email)->first();
            $user->status = config('const.USER_STATUS_ACTIVATED');
            $userId = User::find($user->id);
            $userId->attachRole(config('const.ROLE_AUTHOR'));
            $user->save();
            $model->delete();
            return redirect('login');
        }else{
            return'already confrmed';
        }
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
