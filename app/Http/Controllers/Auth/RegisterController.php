<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DB;
use Carbon\Carbon;
use App\Models\RegisterSetting;
use App\Models\Domain;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/register-verified';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function registered(Request $request, User $user)
    {
        if ($user instanceof MustVerifyEmail) {
            return response()->json(['status' => trans('verification.sent')]);
        }

        return response()->json($user);
    }

    /**
     * Register for event outside of vue app
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return Response
     */
    public function registerEvent(Request $request)
    {
        // todo: move this to middleware!
      $domain = explode('.', $request->getHost());

      $subdomain_first = $domain[0];

      $domain = Domain::where('domain', $subdomain_first)->first();

      return view('register')->with('domain', $domain);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'domain_id' => $data['id'],
            'register_metadata' => isset($data['register_metadata']) ? json_encode($data['register_metadata']) : ''
        ]);
    }

    public function csvRegister(Request $request)
    {
        $adminData = $request->adminData;
        $admin = User::create([
            'name'  => $adminData['first_name'].' '.$adminData['last_name'],
            'email' => $adminData['email'],
            'password'  => bcrypt($adminData['password']),
            'role'  => 2,
            'company'   => $adminData['company'],
            'title' => $adminData['title'],
            'phone' => $adminData['phone'],
            'address' => $adminData['address'],
            'zipcode' => $adminData['zipcode'],
            'type'  => 'csv'
        ]);

        $userdata = $request->userData;
        foreach($userdata as $user) {
            User::create([
                'name'  => $user['user_name'],
                'phone'  => $user['phone'],
                'email'  => $user['email'],
                'parent'    => $admin->id,
                'role'  => 3
            ]);
        }

        $users = User::where('parent', auth()->user()->id)->get();

        return response()->json($users);
    }

    public function codeCheck(Request $request) {
        $check = User::where([
            'verify_code' => $request->code,
            'email' => $request->email
        ])->first();

        if (!empty($check)) {
            return response()->json(['check' => 1, 'id' => $check->id]);
        }

        return response()->json(['check' => 0, 'data' => [
            'verify_code' => $request->code,
            'email' => $request->email
        ]]);
    }

    public function getCode(Request $request) {
        return response()->json(User::where('verify_code', $request->code)->get());
    }

    public function getAdmin(Request $request) {
        return response()->json(User::where('verify_code', $request->code)->get());
    }

    public function codeRegister(Request $request) {
        $user = User::find($request->id);
        $user->password = bcrypt($request->password);
        $user->status = 1;
        $user->save();
        return response()->json($user);
	}
	
	public function register(Request $request) {
		$user = User::find($request->code);

		if(!$user) {
			return response()->json(['error' => 'failed to find user', $request->all()]);
		}

		$user->password = bcrypt($request->password);
		$user->verify_code = '';
		$user->save();

		return response()->json(['status' => 200, 'user' => $user]);
	}
}
