<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    protected $redirectTo = '/register-verified';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the user's email address as verified.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, User $user)
    {
        if (! URL::hasValidSignature($request)) {
            return response()->json([
                'status' => trans('verification.invalid'),
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => trans('verification.already_verified'),
            ], 400);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json([
            'status' => trans('verification.verified'),
        ]);
    }

    /**
     * Mark the user's email address as verified and serve settings page for event user profile
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return Response
     */
    public function verifyAvatar(Request $request, User $user)
    {
        $logged_in = false;

        if (false && ! URL::hasValidSignature($request)) {
            return view('register-failure');
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        $user_logged = User::find(session('register_user'));

        if (!empty($user_logged) && $user->id === $user_logged->id) $logged_in = true;
        $data = ((empty($user->register_metadata)) ? [] : (array) json_decode($user->register_metadata));

        foreach ($data as $field => $value) {
            $fieldnew = str_replace("'", "", $field);
            if ($field != $fieldnew) {
                $data[$fieldnew] = $value;
            }
        }

        $name_parts = explode(' ', $user->name);
        $data['first_name'] = $name_parts[0];
        $data['last_name'] = $name_parts[count($name_parts)-1];

        if (!isset($data['industry'])) $data['industry'] = [];

        return view('register-verified')->with([
            'user' => $user,
            'data' => $data,
            'logged_in' => $logged_in,
            'edit' => false
        ]);
    }

    /**
     * Settings page for event user profile
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function updateSettings(Request $request)
    {
        $user = User::find(session('register_user'));

        $public = $request->post('make_public');

        $location = 'mbda/private_avatars';
        if (intval($public) == 1) {
            $location = 'mbda/public_avatars';
        }

        $data = ((empty($user->register_metadata)) ? [] : (array) json_decode($user->register_metadata));

        if ($request->file('file')) {
            $path = $request->file('file')->store($location);
            $user->avatar = $path;
    
            $data['make_public'] = $public;

            $user->register_metadata = json_encode($data);
            $user->save();

            return response()->json($request->user());
        } else {
            $data['make_public'] = $public;

            $submit_data = $request->post('register_metadata');
            foreach ($submit_data as $field => $value) {
                $fieldnew = str_replace("'", "", $field);
                $data[$fieldnew] = $value;
            }

            $user->name = $data['first_name'] . ' ' . $data['last_name'];
        }

        foreach ($data as $field => $value) {
            $fieldnew = str_replace("'", "", $field);
            if ($field != $fieldnew) {
                $data[$fieldnew] = $value;
                unset($data[$field]);

            }
        }

        $user->register_metadata = json_encode($data);
        $user->save();
        
        $name_parts = explode(' ', $user->name);
        $data['first_name'] = $name_parts[0];
        $data['last_name'] = $name_parts[count($name_parts)-1];

        if (!isset($data['industry'])) $data['industry'] = [];

        return view('register-verified')->with([
            'user' => $user,
            'data' => $data,
            'logged_in' => true,
            'edit' => true
        ]);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (is_null($user)) {
            throw ValidationException::withMessages([
                'email' => [trans('verification.user')],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => [trans('verification.already_verified')],
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => trans('verification.sent')]);
    }
}
