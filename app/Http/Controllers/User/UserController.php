<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Request;
use File;

use App\Notifications\RegisterNotification;
use App\User;
use App\Models\Domain;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function get()
    {
        try {
            $siteId = Request::get('siteId');
            $role = Request::get('role');
            $perPage = Request::get('perPage');
            $domain = Domain::find($siteId);

            $input = Request::get('input');
            if (User::where('name', 'LIKE', '%' . $input . '%')->count()) {
                $userData = User::where('name', 'LIKE', '%' . $input . '%')
                ->where('domain_id', $siteId)
                ->orderBy('role', 'asc');

                if ($role != 0) {
                    $userData = $userData->where('role', $role);
                }
                
                if ($role == 3) {
                    $userData = $userData->where('type', Request::get('type'));
                }
    
                $status = Request::get('status');
                if ($status != 2) {
                    $userData = $userData->where('status', $status);
                }
                $data['userData'] = $userData->paginate($perPage);
            } else {
                $data['userData'] = null;
            }

            $data['allUserData'] = User::where('domain_id', $siteId)->orWhere('role', 1)->get();
            $data['adminData'] = $domain->admin;

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setStatus()
    {
        try {
            $list = Request::get('list');
            $role = Request::get('role');
            $siteId = Request::get('siteId');
            $perPage = Request::get('perPage');
            $status = Request::get('status');

            foreach($list as $id) {
                $user = User::find($id);
    
                if ($status != 2) {
                    $user->status = $status;
                    $user->save();
                } else {
                    if ($user->avatar != 'default.jpg') {
                        File::delete(public_path('data/'.$user->avatar));
                    }
                    $user->delete();
                }
            }
    
            $input = Request::get('input');
            if (User::where('name', 'LIKE', '%' . $input . '%')->count()) {
                $userData = User::where('name', 'LIKE', '%' . $input . '%')
                ->where('domain_id', $siteId)
                ->orderBy('role', 'asc');

                if ($role != 0) {
                    $userData = $userData->where('role', $role);
                }
                
                if ($role == 3) {
                    $userData = $userData->where('type', Request::get('type'));
                }
    
                $status = Request::get('status');
                if ($status != 2) {
                    $userData = $userData->where('status', $status);
                }
                $data['userData'] = $userData->paginate($perPage);
            } else {
                $data['userData'] = null;
            }

            $data['allUserData'] = User::where('domain_id', $siteId)->orWhere('role', 1)->get();
            
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function set()
    {
        try {
            $id = Request::get('id');
            $code = Request::get('code');
            $siteId = Request::get('siteId');
            $perPage = Request::get('perPage');
            if ($id == 0) {
                $user = new User();
                $user->status = 1;
            } else {
                $user = User::find($id);
            }

            $role = Request::get('role');
            if ($role == 0) {
                $userRole = Request::get('userRole');
                $type = Request::get('type');
            }

            $user->name = Request::get('name');
            $user->domain_id = $siteId;
            $user->email = Request::get('email');
            $user->verify_code = $code;
            $user->bio = Request::get('bio');
            if ($role == 0) {
                $user->role = $userRole;
                $user->type = $type;
            } else {
                $user->role = $role;
            }
            $user->save();

            // $user->notify(new RegisterNotification($code, null));

            $input = Request::get('input');
            if (User::where('name', 'LIKE', '%' . $input . '%')->count()) {
                $userData = User::where('name', 'LIKE', '%' . $input . '%')
                ->where('domain_id', $siteId)
                ->orderBy('role', 'asc');

                if ($role != 0) {
                    $userData = $userData->where('role', $role);
                }
                
                if ($role == 3) {
                    $userData = $userData->where('type', Request::get('type'));
                }
    
                $status = Request::get('status');
                if ($status != 2) {
                    $userData = $userData->where('status', $status);
                }
                $data['userData'] = $userData->paginate($perPage);
            } else {
                $data['userData'] = null;
            }
            
            $data['allUserData'] = User::where('domain_id', $siteId)->orWhere('role', 1)->get();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function resend()
    {
        try {
            $codeList = Request::get('codeList');
            $list = Request::get('list');
            foreach($list as $key => $id) {
                $user = User::find($id);
                $user->verify_code = $codeList[$key];
                $user->save();
                // $user->notify(new RegisterNotification($code, null));
            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function csv()
    {
        try {
            $id = Request::get('id');
            $role = Request::get('role');
            $perPage = Request::get('perPage');
            $csvData = Request::get('csvData');
            $type = Request::get('type');
            foreach($csvData as $data) {
                $meta = $data['meta'];
                if ($meta == '') {
                    $meta = null;
                } else {
                    $meta = json_encode($meta);
                }
                if (User::where('email', $data['email'])->count()) {
                    $user = User::where('email', $data['email'])->first();
                } else {
                    $user = new User();
                }
                $user->domain_id = $id;
                $user->role = $role;
                $user->name = $data['f_name'].' '.$data['l_name'];
                $user->title = $data['field2'];
                $user->email = $data['email'];
                $user->company = $data['field1'];
                $user->address = $data['field3'].', '.$data['field4'].', '.$data['field5'];
                $user->parent = auth()->user()->id;
                $user->verify_code = rand(1000, 9999);
                $user->bio = $data['field0'];
                $user->register_metadata = $meta;
                if ($role == 3) {
                    $user->type = $type;
                }
                $user->save();
            }
            $userData = User::where('domain_id', $id);
            if ($role) {
                $userData = $userData->where('role', $role);
            }
            if ($role == 3) {
                $userData = $userData->where('type', $type);
            }
            $userData = $userData->paginate($perPage);
            return response()->json($userData);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
