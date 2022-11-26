<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Request;
use App\User;

use App\Notifications\RegisterNotification;
use App\Notifications\ApprovedNotification;

class AdminController extends Controller
{
    public function get()
    {
        $adminData = $this->getAdmin();
        $users = $this->getUsers();
        return response()->json(compact('adminData', 'users'));
    }

    public function setStatus() {
        $userIds = Request::get('users');
        $status = Request::get('status');

        foreach($userIds as $id) {
            $admin = User::find($id);

            $userData = User::where('parent', $id)->get();
            foreach($userData as $user) {
                if ($status == 0) {
                    $user->status = $status;
                    $user->save();
                } else if ($status == 2) {
                    if ($user->avatar != 'default.jpg') {
                        File::delete(public_path('data/'.$user->avatar));
                    }
                    $user->delete();
                }
            }

            if ($status != 2) {
                $admin->status = $status;
                $admin->save();
            } else {
                if ($admin->avatar != 'default.jpg') {
                    File::delete(public_path('data/'.$admin->avatar));
                }
                $admin->delete();
            }
        }

        $adminData = $this->getAdmin();
        return response()->json($adminData);
    }

    public function set() {
        try {
            $status = Request::get('status');

            $admin = User::find(Request::get('id'));
            $admin->status = $status;
            $admin->save();

            $users = Request::get('users');

            foreach ($users as $user) {
                if (isset($user['disable']) && $user['disable']) {
                    $editUser = User::find($user['id']);
                    $editUser->status = 0;
                } else {
                    $editUser = User::find($user['id']);
                    $editUser->status = $status;
                }
                $editUser->save();
            }

            User::find(Request::get('id'))->notify(new ApprovedNotification());

            $users = $this->getUsers();
            $adminUsers = $this->getAdmin();
            return response()->json(compact('users', 'adminUsers'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function add()
    {
        $email = Request::get('email');
        $code = Request::get('code');

        $user = User::create([
            'role'  => 2,
            'name'  => Request::get('name'),
            'email' => $email,
            'verify_code'   => $code,
            'status' => 1
        ]);

        $user->notify(new RegisterNotification($code, null));

        $adminData = $this->getAdmin();

        return response()->json($adminData);
    }

    private function getAdmin() {
        return User::where('role', 2)->get();
    }

    private function getUsers() {
        return User::where('role', '>', 2)->get();
    }
}
