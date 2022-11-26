<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Request;
use File;

use App\Models\Booth;
use App\Models\Header;
use App\Models\Template;
use App\Models\Upload;
use App\Models\Tab;

class BoothController extends Controller
{
    public function get()
    {
        try {
            $data = Booth::where('domain_id', Request::get('siteId'))->where('type', 'booth')->get();
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getItem()
    {
        try {
            $id = Request::get('id');
            $this->bladeContent['page'] = Booth::where('id', $id)->with('template')->with('header.tab.media')->first();
            $this->bladeContent['resource'] = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->where('type', 'booth')
            ->get();

            return response()->json($this->bladeContent);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function set()
    {
        try {
            $newData = Booth::find(Request::get('id'));

            $items = json_decode(Request::get('data'));

            foreach($items as $key => $item) {
                $file = Request::file('media'.$key);
                if ($file) {
                    File::delete(public_path('data/'.$item->url));
                    $new_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('data/'), $new_name);
                    $item->url = $new_name;
                }
            }
            
            $newData->data = $items;
            $newData->save();

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
                $data->type = 'booth';
            } else {
                $data = Header::find($editId);
                $data->title = $title;
            }

            $data->save();

            $resource = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->get();
            $data = Booth::where('id', Request::get('id'))
            ->with('header.tab.media')->first();

            return response()->json(compact('resource', 'data'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function head()
    {
        try {
            $data = Booth::find(Request::get('id'));

            $data->header_style = Request::get('style');

            $data->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function order()
    {
        try {
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

            $data = Booth::where('id', Request::get('id'))
            ->with('header.tab.media')->first();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
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
        $data = Booth::where('id', Request::get('id'))
        ->with('header.tab.media')->first();

        return response()->json(compact('resource', 'data'));
    }

    public function del()
    {
        try {
            $id = Request::get('id');

            foreach (Header::where('booth_id', $id)->get() as $header) {
                $header->delete();
            }
            
            Booth::find($id)->delete();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function publish()
    {
        try {
            $booth = Booth::find(Request::get('id'));

            $file = Request::file('template');
            if ($file) {
                File::delete(public_path('data/'.$booth->template->media));
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                $temp = Template::create([
                    'domain_id' =>  Request::get('siteId'),
                    'user_id'   =>  auth()->user()->id,
                    'type'      =>  4,
                    'media'     =>  $new_name
                ]);
                $booth->template_id = $temp->id;
            }

            $booth->status = 1;
            $booth->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    
    public function setTitle()
    {
        try {
            $type = Request::get('type');
            $id = Request::get('id');
            if ($type < 2) {
                $booth = Booth::find($id);
            } else {
                $booth = Poster::find($id);
            }
            $booth->title = Request::get('title');
            $booth->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function tabTitle()
    {
        try {
            $data = Tab::find(Request::get('id'));
            $data->title = Request::get('title');
            $data->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function addAsset()
    {
        try {
            $file = Request::file('file');
            $link = Request::get('file');
            $id = Request::get('id');
            $editId = Request::get('editId');
            $title = Request::get('title');
            $type = Request::get('type');
            $count = Upload::where('tab_id', $id)->where('type', $type)->count();
            if ($file) {
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                if ($editId == 0) {
                    Upload::create([
                        'tab_id'    =>  $id,
                        'title'     =>  $title,
                        'item'      =>  $new_name,
                        'order'     =>  $count,
                        'type'      =>  $type
                    ]);
                } else {
                    $data = Upload::find($editId);
                    File::delete(public_path('data/'.$data->item));
                    $data->item = $new_name;
                    $data->title = $title;
                    $data->save();
                }
            }

            $data = Booth::where('id', Request::get('boothId'))
            ->with('header.tab.media')->first();
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function content()
    {
        try {
            $tabs = Request::get('tabs');
            foreach(Request::get('data') as $key => $data) {
                foreach ($tabs[$key] as $tab) {
                    if(isset($tab['id'])) {
                        $item = Tab::find($tab['id']);
                    } else {
                        $item = new Tab();
                    }
                    $item->title = $tab['title'];
                    if (isset($tab['content'])) {
                        $item->content = $tab['content'];
                    }
                    $item->index = $key;
                    $item->header_id = $data['id'];
                    $item->save();
                }
            }

            $data = Booth::where('id', Request::get('id'))
            ->with('header.tab.media')->first();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function delTab()
    {
        try {
            $tab = Tab::find(Request::get('delId'));
            foreach($tab->media as $media) {
                File::delete(public_path('data/'.$media->item));
                Upload::find($media->id)->delete();
            }
            $tab->delete();

            $data = Booth::where('id', Request::get('id'))
            ->with('header.tab.media')->first();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function mediaStatus()
    {
        try {
            $status = Request::get('status');
            foreach(Request::get('items') as $key) {
                $data = Upload::find($key);
                if ($status < 2) {
                    $data->status = $status;
                    $data->save();
                } else {
                    File::delete(public_path('data/'.$data->item));
                    $data->delete();
                }
            }

            $data = Booth::where('id', Request::get('id'))
            ->with('header.tab.media')->first();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
