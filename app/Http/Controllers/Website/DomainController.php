<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Notifications\NewUserImported;
use Request;
use Validator;
use File;

use App\Models\Domain;
use App\Models\Booth;
use App\Models\Poster;
use App\Models\RegisterSetting;
use App\Models\Session;
use App\Models\Event;
use App\User;

use App\Notifications\RegisterNotification;

class DomainController extends Controller
{
    public function get() {
        try {
            $id = Request::get('id');
            $domains = [];
            $users = User::where('parent', auth()->user()->id)->get();
            if ($id) {
                $domain = Domain::find($id);
                return response()->json(compact('domain', 'users'));
            } else {
                if (auth()->user()->role == 1) {
                    $domains = Domain::all();
                } else {
                    $domains = Domain::where('user_id', auth()->user()->id)->get();
                }
                
                return response()->json(compact('users', 'domains'));
            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function set(Request $request) {
        try {
            $validation = Validator::make(Request::all(), $this->rule($request));

            if ($validation->passes()) {
                if (!Domain::where('domain', Request::get('domain'))->count()) {
                    Domain::create([
                        'user_id' => auth()->user()->id,
                        'title'   => Request::get('title'),
                        'domain'  => Request::get('domain'),
                    ]);
                }
                
                $data = Domain::where('user_id', auth()->user()->id)->get();
                return response()->json($data);
            } else {
                return response()->json([
                    'errors' => $validation->errors()
                ]);
            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function status() {
        try {
            foreach (Request::get('items') as $data) {
                $item = Domain::find($data);
                if (Request::get('status') !== 2) {
                    $item->status = Request::get('status');
                    $item->save();
                } else {
                    File::delete(public_path('data/'.$item->logo));
                    $item->delete();
                }
            }
            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function domain() {
        try {
            $data = Domain::find(Request::get('id'));
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function sessionData() {
        try {
            $data = Session::find(Request::get('id'));
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function session() {
        try {
            $session = Session::with('track')->with('event')->with('uploads')->find(Request::get('id'));
            $emailList = [];
            if ($session->presenter) {
                $emailList = json_decode($session->presenter);
            }
            $presenterData = [];
            if (count($emailList)) {
                foreach($emailList as $email) {
                    if (User::where('email', $email)->count()) {
                        $user = User::where('email', $email)->first();
                        if ($user->avatar != 'default.jpg' && file_exists(public_path('assets/img/avatar/'.$user->avatar))) {
                            $presenterData[] = $user;
                        }
                    }
                }
            }
            return response()->json(compact('session', 'presenterData'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function host() {
        try {
            $domain = Domain::where('domain', Request::get('domain'))->first();
            $fields = RegisterSetting::where('domain_id', $domain->id)
            ->where('disabled', false)
            ->orderBy('order', 'asc')
            ->get();
            return response()->json(compact('domain', 'fields'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function data() {
        try {
            $site = Domain::where('domain', Request::get('host'))->first();

            $users = User::where('parent', $site->user_id)->get();
            return response()->json(compact('site', 'users'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function link() {
        $data = Request::get('data');
        $users = User::where('parent', auth()->user()->id)->get();

        $id = Request::get('id');

        foreach ($users as $key => $user) {
            $code = $data[$key]['code'];

            $user->verify_code = $code;
            $user->type = $data[$key]['type']['value'];
            $user->domain_id = $id;
            $user->save();
            
            $user->notify(new RegisterNotification($code, Request::get('url')));
        }
        return response()->json(['success' => true]);
    }

    public function csvRegister()
    {
        $adminData = Request::get('adminData');
        $admin = auth()->user();

        $admin->name  = $adminData['first_name'].' '.$adminData['last_name'];
        $admin->password  = bcrypt($adminData['password']);
        $admin->company   = $adminData['company'];
        $admin->title = $adminData['title'];
        $admin->phone = $adminData['phone'];
        $admin->address = $adminData['address'];
        $admin->zipcode = $adminData['zipcode'];
        $admin->type  = 'csv';

        $admin->save();

        $url = Request::get('url');

        $userData = Request::get('userData');

        foreach($userData as $user) {
            $user = User::create([
                'name'  => $user['user_name'],
                'phone'  => $user['phone'],
                'email'  => $user['email'],
                'parent'    => $admin->id,
                'domain_id' => $url,
                'role'  => 3
            ]);
        }

        // User::find(5)->notify(new NewUserImported($admin));

        $users = User::where('parent', $admin->id)->get();

        return response()->json($users);
    }

    public function note()
    {
        try {
            $data = Domain::find(Request::get('id'));
            $data['note'.Request::get('type')] = Request::get('note');
            $data->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getInfo()
    {
        try {
            $id = Request::get('id');
            $this->bladeContent['booth'] = Booth::where('domain_id', $id)->where('type', 0)->count();
            $this->bladeContent['sponsor'] = Booth::where('domain_id', $id)->where('type', 1)->count();
            $this->bladeContent['poster'] = Poster::where('domain_id', $id)->count();
            $this->bladeContent['users'] = User::where('domain_id', $id)->count();
            $this->bladeContent['timezone'] = date_default_timezone_get();
            $this->bladeContent['sessionCount'] = 0;
            foreach(Event::where('domain_id', $id)->with('session')->get() as $event) {
                $this->bladeContent['sessionCount'] += $event->session->count();
            }
            $this->bladeContent['start'] = Domain::find($id)->start;
            $this->bladeContent['end'] = Domain::find($id)->end;
            return response()->json($this->bladeContent);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function user()
    {
        return response()->json(User::find(Request::get('id')));
    }

    private function rule($request) {
        $rule['title'] = 'required';
        $rule['domain'] = 'required|regex:/^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]+$/|unique:domains';
        return $rule;
    }

    private function userRule($request) {
        $rule['name'] = 'required|max:255';
        $rule['email'] = 'required|email|max:255|unique:users';
        $rule['password'] = 'required|min:6|confirmed';
        return $rule;
    }

    public function titleGet()
    {
        try {
            return response()->json(Domain::find(Request::get('id'))->custom_name);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function titleSet()
    {
        try {
            $data = Domain::find(Request::get('id'));
            $data->custom_name = Request::get('titleData');
            $data->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
