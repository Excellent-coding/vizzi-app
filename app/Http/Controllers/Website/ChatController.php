<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Request;

use App\Models\Chat;
use App\User;

class ChatController extends Controller
{
    public function chatSet()
    {
        try {
            $item = Request::get('item');
            $id = Request::get('id');
            $type = Request::get('type');
            if ($type !== 'info' && $type !== 'help') {
                $count = Chat::where('domain_id', $id)
                ->where('user_id', auth()->user()->id)
                ->where('support_id', $item['user_id'])
                ->where('item_id', $item['id'])
                ->where('type', $type)
                ->count();
                if ($count) {
                    $data = Chat::where('domain_id', $id)
                    ->where('user_id', auth()->user()->id)
                    ->where('support_id', $item['user_id'])
                    ->where('item_id', $item['id'])
                    ->where('type', $type)
                    ->first();
                } else {
                    $data = new Chat();
                }
                $data->user_id = auth()->user()->id;
                $data->support_id = $item['user_id'];
                $data->domain_id = $id;
                $data->item_id = $item['id'];
                $data->type = $type;
                $data->save();
            } else {
                $count = Chat::where('domain_id', $id)
                ->where('user_id', auth()->user()->id)
                ->where('type', $type)
                ->count();
                if ($count) {
                    $count = Chat::where('domain_id', $id)
                    ->where('user_id', auth()->user()->id)
                    ->where('type', $type)
                    ->first();
                } else {
                    $data = new Chat();
                    $data->user_id = auth()->user()->id;
                    $data->domain_id = $id;
                    $data->type = $type;
                    $data->save();
                }
            }

            return response()->json($data->id);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function chatGet()
    {
        try {
            $id = Request::get('id');
            $data['booth'] = Chat::where('domain_id', $id)
            ->where('user_id', auth()->user()->id)
            ->where('type', 0)
            ->with('booth.user')
            ->get();
            $data['sponsor'] = Chat::where('domain_id', $id)
            ->where('user_id', auth()->user()->id)
            ->where('type', 1)
            ->with('booth.user')
            ->get();
            $data['poster'] = Chat::where('domain_id', $id)
            ->where('user_id', auth()->user()->id)
            ->where('type', 2)
            ->with('poster.user')
            ->get();
            $data['helpData'] = Chat::where('id', $id)->where('type', 'help')->with('user')->first();
            $data['infoData'] = Chat::where('id', $id)->where('type', 'info')->with('user')->first();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function chatAdminGet()
    {
        try {
            $data = Chat::where('domain_id', Request::get('id'))
            ->where('support_id', auth()->user()->id)
            ->with('user')
            ->with('poster')
            ->with('booth')
            ->get();
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function helpUser()
    {
        try {
            $helpUsers = User::where('role', 1)->get();
            foreach($helpUsers as $user) {
                if (isset($user->avatar) && $user->avatar != 'default.jpg' && file_exists(public_path('assets/img/avatar/'.$user->avatar))) {
                    $helpData[] = $user;
                }
            }
            $infoUsers = User::where('role', 1)
            ->orWhere(function($query) {
                $query->where('domain_id', Request::get('id'))
                ->where('role', 2)
                ->orWhere('role', 6);
            })->get();
            foreach($infoUsers as $user) {
                if (isset($user->avatar) && $user->avatar != 'default.jpg' && file_exists(public_path('assets/img/avatar/'.$user->avatar))) {
                    $infoData[] = $user;
                }
            }
            if (!isset($helpData)) {
                $helpData = [];
            } 
            if (!isset($infoData)) {
                $infoData = [];
            } 
            return response()->json(compact('infoData', 'helpData'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
