<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Request;

use App\User;
use App\Models\Domain;

class ChatController extends Controller
{
    public function contacts()
    {
        $user = auth()->user();
        $siteId = Request::get('siteId');

        $managers = User::where('id', '<>', $user->id)
        ->where('domain_id', $siteId)
        ->where('role', '<>', 5)
        ->get();

        $domain = Domain::find($siteId);
        
        $admin = User::find($domain->user_id);

        $users = User::where('id', '<>', $user->id)
        ->where('domain_id', $siteId)
        ->where('role', 5)
        ->get();

        return response()->json(compact('managers', 'users', 'admin'));
    }
}
