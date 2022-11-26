<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Request;
use Validator;
use File;

use App\Models\Page;
use App\Models\Domain;
use App\Models\Booth;
use App\Models\Poster;
use App\Models\Hall;

class MenuController extends Controller
{
    public function get() {
        try {
            $id = Request::get('id');
            $data['menus'] = Page::where('domain_id', $id)->orderBy('order', 'asc')->get();
            $data['header'] = Page::where('domain_id', $id)->where('status', 1)->orderBy('order', 'asc')->get();
            $data['resource'] = Page::where('domain_id', $id)->where('status', 0)->orderBy('order', 'asc')->get();
            $data['domain'] = Domain::find($id);

            $data['boothData'] = Hall::where('domain_id', $id)->where('type', 0)->get();
            $data['sponsorData'] = Hall::where('domain_id', $id)->where('type', 1)->get();
            $data['posterData'] = Hall::where('domain_id', $id)->where('type', 2)->get();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function set(Request $request) {
        try {
            $validation = Validator::make(Request::all(), $this->rule($request));

            if ($validation->passes()) {
                $editId = Request::get('editId');
                $id = Request::get('id');

                if ($editId == 0) {
                    $page = new Page();
                } else {
                    $page = Page::find($editId);
                }
                $page->domain_id = $id;
                $page->title = Request::get('title');
                $page->type = Request::get('type');
                $page->order = Page::where('domain_id', $id)->count();
                $exhibit = Request::get('exhibit');

                $page->url = '/'.Request::get('url');
                if ($exhibit) {
                    $page->hall_id = $exhibit;
                }

                $page->save();

                $menus = Page::where('domain_id', $id)->orderBy('order', 'asc')->get();
                $header = Page::where('domain_id', $id)->where('status', 1)->orderBy('order', 'asc')->get();
                $resource = Page::where('domain_id', $id)->where('status', 0)->orderBy('order', 'asc')->get();
                return response()->json(compact('resource', 'header', 'menus'));
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
                $item = Page::find($data);
                if (Request::get('status') !== 2) {
                    $item->status = Request::get('status');
                    $item->save();
                } else {
                    File::delete(public_path('data/'.$item->background));
                    $item->delete();
                }
            }

            $id = Request::get('id');

            $menus = Page::where('domain_id', $id)->orderBy('order', 'asc')->get();
            $header = Page::where('domain_id', $id)->where('status', 1)->orderBy('order', 'asc')->get();
            $resource = Page::where('domain_id', $id)->where('status', 0)->orderBy('order', 'asc')->get();
            return response()->json(compact('resource', 'header', 'menus'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function order() {
        foreach (Request::get('header') as $key => $id) {
            $data = Page::find($id);
            $data->order = $key;
            $data->status = 1;
            $data->save();
        }
        foreach (Request::get('source') as $key => $id) {
            $data = Page::find($id);
            $data->order = $key;
            $data->status = 0;
            $data->save();
        }
    }
    
    public function host() {
        $domain = Domain::find(Request::get('id'));
        return response()->json($domain);
    }

    public function head() {
        try {
            $domain = Domain::find(Request::get('id'));
            $domain->style = Request::get('style');
            $domain->save();
            return response()->json($domain);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function title()
    {
        try {
            $data = Domain::find(Request::get('id'));

            $file = Request::file('file');
            if ($file) {
                File::delete(public_path('data/'.$data->logo));
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                $data->logo = $new_name;
            }

            $file1 = Request::file('file1');
            if ($file1) {
                File::delete(public_path('data/'.$data->darkLogo));
                $new_name = rand() . '.' . $file1->getClientOriginalExtension();
                $file1->move(public_path('data/'), $new_name);
                $data->darkLogo = $new_name;
            }

            $video = Request::file('video');
            if ($video) {
                File::delete(public_path('data/'.$data->video));
                $new_name = rand() . '.' . $file1->getClientOriginalExtension();
                $file1->move(public_path('data/'), $new_name);
                $data->video = $video;
            } else {
                $link = Request::get('video');
                if ($link) {
                    $data->video = $link;
                }
            }
 
            $data->title = Request::get('title');
            
            $data->start = Request::get('start');
            $data->end = Request::get('end');

            $data->save();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    private function rule($request) {
        $rule['title'] = 'required';
        $rule['type'] = 'required';
        return $rule;
    }
}
