<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Request;

use App\Models\Booth;
use App\Models\Header;

class SponsorController extends Controller
{
    public function upload()
    {
        try {
            $data = new Booth();

            $data->domain_id = Request::get('siteId');
            $data->user_id = auth()->user()->id;

            $file = Request::file('file');
            if ($file) {
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                $data->media = $new_name;
            }
            $data->type = 2;
            $data->save();

            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function get()
    {
        try {
            $data = Booth::where('domain_id', Request::get('siteId'))->where('type', 2)->get();
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getItem()
    {
        try {
            $id = Request::get('id');

            $page = Booth::find($id);

            $resource = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->get();
            $header = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 1)
            ->get();

            return response()->json(compact('page', 'resource', 'header'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function set()
    {
        try {
            $newData = Booth::find(Request::get('id'));

            $data = Request::all();

            $media = Request::file('media');

            if ($media) {
                $new_name = rand() . '.' . $media->getClientOriginalExtension();
                $media->move(public_path('data/'), $new_name);
                $data['media'] = $new_name;
            }

            $mediaData = $newData->data;
            if (is_null($mediaData)) {
                $mediaData = [];
            } else {
                $mediaData = json_decode($mediaData);
            }
            array_push($mediaData, $data);

            $newData->data = $mediaData;
            $newData->save();

            return response()->json($newData);

        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function update()
    {
        try {
            $data = Booth::find(Request::get('id'));
            $data->data = Request::get('data');
            $data->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    
    public function getHeader()
    {
        try {
            $id = Request::get('id');

            $resource = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->get();
            $header = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 1)
            ->get();
            $style = Booth::find($id)->header_style;

            return response()->json(compact('resource', 'header', 'style'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setHeader()
    {
        try {
            $editId = Request::get('editId');
            $title = Request::get('title');
            $id = Request::get('id');

            if ($editId == 0) {
                $data = new Header();
                $data->title = $title;
                $data->booth_id = $id;
                $data->order = Header::where('booth_id', $id)->count();
            } else {
                $data = Header::find($editId);
                $data->title = $title;
            }

            $data->save();

            $resource = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->get();
            $header = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 1)
            ->get();

            return response()->json(compact('resource', 'header'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function head()
    {
        try {
            $data = Booth::find(Request::get('id'));

            $data->header_style = Request::get('headerStyle');

            $data->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function order()
    {
        foreach (Request::get('header') as $key => $id) {
            $data = Header::find($id);
            $data->order = $key;
            $data->status = 1;
            $data->save();
        }
        foreach (Request::get('source') as $key => $id) {
            $data = Header::find($id);
            $data->order = $key;
            $data->status = 0;
            $data->save();
        }
    }

    public function content()
    {
        foreach (Request::get('data') as $data) {
            $header = Header::find($data['id']);
            $header->content = $data['contentData'];
            $header->save();
        }
    }

    public function status()
    {
        $status = Request::get('status');
        foreach (Request::get('items') as $id) {
            $data = Header::find($id);
            if ($status != 2) {
                $data->status = $status;
                $data->save();
            } else {
                $data->delete();
            }
        }

        $id = Request::get('id');
        $resource = Header::where('booth_id', $id)
        ->orderBy('order', 'asc')
        ->where('status', 0)
        ->get();
        $header = Header::where('booth_id', $id)
        ->orderBy('order', 'asc')
        ->where('status', 1)
        ->get();

        return response()->json(compact('resource', 'header'));
    }
}
