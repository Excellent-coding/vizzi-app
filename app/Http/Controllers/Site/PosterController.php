<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Request;
use File;

use App\Models\Booth;
use App\Models\Poster;
use App\Models\Header;
use App\Models\Introduction;
use App\Models\Category;
use App\Models\Tab;
use App\Models\Upload;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PosterController extends Controller
{
    public function upload()
    {
        try {
            $poster = Poster::find(Request::get('id'));

            $poster->type = 1;
            $poster->title = Request::get('title');
            $file = Request::file('file');
            if ($file) {
                File::delete(public_path('data/'.$poster->media));
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                $poster->media = $new_name;
            }

            $poster->save();

            return response()->json($poster);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function multipleUpload()
    {
        try {
            $poster = Poster::find(Request::get('id'));
            $poster->type = 2;
            $poster->title = Request::get('title');

            $media = [];
            $file1 = Request::file('file0');
            if ($file1) {
                $new_name = rand() . '.' . $file1->getClientOriginalExtension();
                $file1->move(public_path('data/'), $new_name);
                array_push($media, $new_name);
            }

            $file2 = Request::file('file1');
            if ($file2) {
                $new_name = rand() . '.' . $file2->getClientOriginalExtension();
                $file2->move(public_path('data/'), $new_name);
                array_push($media, $new_name);
            }

            $file3 = Request::file('file2');
            if ($file3) {
                $new_name = rand() . '.' . $file3->getClientOriginalExtension();
                $file3->move(public_path('data/'), $new_name);
                array_push($media, $new_name);
            }

            $poster->media = $media;

            $poster->save();

            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function advancedUpload()
    {
        try {
            $poster = Poster::find(Request::get('id'));

            $poster->type = 3;
            $poster->title = Request::get('title');
            $layers = json_decode(Request::get('layers'));
            $medias = explode(', ', $poster->media);
            $poster->posterHeader = Request::get('posterHeader');
            foreach($layers as $key => $layer) {
                $file = Request::file('file'.$key);
                if ($file) {
                    File::delete(public_path('data/'.$layer->media->file));
                    $new_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('data/'), $new_name);
                    $layer->media->file = $new_name;
                } else if ($key < count($medias) && $medias[$key]) {
                    $layer->media->file = $medias[$key];
                }
            }
            $poster->layers = $layers;
            $poster->save();
            
            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function get()
    {
        try {
            $id = Request::get('id');

            $posterItem = Poster::where('id', $id)->with('category.backdrop')->with('header.tab.media')->first();

            $resource = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->where('type', 'poster')
            ->get();
            
            return response()->json(compact('posterItem', 'resource'));
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

            $menus = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('type', 'poster')
            ->get();
            $resource = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->where('type', 'poster')
            ->get();
            $header = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 1)
            ->where('type', 'poster')
            ->get();
            $style = Booth::find($id)->header_style;

            return response()->json(compact('resource', 'header', 'style', 'menus'));
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
                $data->type = 'poster';
            } else {
                $data = Header::find($editId);
                $data->title = $title;
            }

            $data->save();

            $menus = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('type', 'poster')
            ->get();
            $resource = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->where('type', 'poster')
            ->get();
            $item = Poster::where('id', $id)->with('header.tab.media')->first();

            return response()->json(compact('resource', 'item', 'menus'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function head()
    {
        try {
            $data = Poster::find(Request::get('id'));
            $data->header_style = Request::get('style');
            $data->save();

            return response()->json(200);
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

            $item = Poster::where('id', Request::get('id'))->with('header.tab.media')->first();

            return response()->json($item);
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
                    $item->content = $tab['content'];
                    $item->index = $key;
                    $item->header_id = $data['id'];
                    $item->save();
                }
            }

            $data = Poster::where('id', Request::get('id'))
            ->with('header.tab.media')->first();

            return response()->json($data);
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
            if ($file) {
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                if ($editId == 0) {
                    Upload::create([
                        'tab_id'    =>  $id,
                        'title'     =>  $title,
                        'item'      =>  $new_name,
                        'order'     =>  Upload::where('tab_id', $id)->where('type', $type)->count(),
                        'type'      =>  2
                    ]);
                } else {
                    $data = Upload::find($editId);
                    File::delete(public_path('data/'.$data->item));
                    $data->item = $new_name;
                    $data->title = $title;
                    $data->save();
                }
            } else if ($link) {
                if ($editId == 0) {
                    Upload::create([
                        'tab_id'    =>  $id,
                        'title'     =>  $title,
                        'item'      =>  $link,
                        'order'     =>  Upload::where('tab_id', $id)->count()
                    ]);
                } else {
                    $data = Upload::find($editId);
                    File::delete(public_path('data/'.$data->item));
                    $data->item = $link;
                    $data->title = $title;
                    $data->save();
                }
            }

            $data = Poster::where('id', Request::get('boothId'))
            ->with('header.tab.media')->first();
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function status()
    {
        try {
            $status = Request::get('status');
            foreach (Request::get('items') as $id) {
                $data = Booth::find($id);
                if ($status != 2) {
                    $data->status = $status;
                    $data->save();
                } else {
                    $data->delete();
                }
            }

            return response()->json(
                Booth::where('domain_id', Request::get('id'))
                ->where('type', 'poster')
                ->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function menuStatus()
    {
        try {
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
    
            $menus = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('type', 'poster')
            ->get();
            $resource = Header::where('booth_id', $id)
            ->orderBy('order', 'asc')
            ->where('status', 0)
            ->where('type', 'poster')
            ->get();
            $item = Poster::where('id', $id)->with('header.tab.media')->first();
    
            return response()->json(compact('resource', 'menus', 'item'));
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

            $data = Poster::where('id', Request::get('id'))->with('header.tab.media')->first();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setText()
    {
        try {
            Introduction::create([
                'domain_id' =>  Request::get('id'),
                'user_id' =>  auth()->user()->id,
                'title' =>  Request::get('title'),
                'content' =>  Request::get('content')
            ]);
            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getData()
    {
        try {
            $id = Request::get('id');
            $introduction = Introduction::where('domain_id', $id)->first();
            $categories = Category::where('domain_id', $id)->get();

            return response()->json(compact('introduction', 'categories'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setCategory()
    {
        try {
            $id = Request::get('id');
            $categoryId = Request::get('categoryId');
            $item = Request::get('item');

            if ($categoryId) {
                $data = Category::find($categoryId);
                $data->name = $item;
                $data->save();
            } else {
                Category::create([
                    'domain_id' =>  $id,
                    'user_id' =>  auth()->user()->id,
                    'category' =>  $item
                ]);
            }

            return response()->json(Category::where('domain_id', $id)->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    public function categoryStatus()
    {
        try {
            $status = Request::get('status');

            foreach (Request::get('items') as $id) {
                $data = Category::find($id);
                if ($status != 2) {
                    $data->status = $status;
                    $data->save();
                } else {
                    $data->delete();
                }
            }

            return response()->json(Category::where('domain_id', Request::get('id'))->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function posterGet()
    {
        try {
            $id = Request::get('id');

            $items = Poster::where('domain_id', $id)
            ->with('category.backdrop')
            ->orderBy('category_id', 'asc')
            ->get();

            $categories = Category::where('domain_id', $id)
            ->get();

            return response()->json(compact('items', 'categories'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function posterSet()
    {
        try {
            $id = Request::get('id');

            $data = new Poster();
            $data->domain_id = $id;
            $data->user_id = auth()->user()->id;
            $data->category_id = Request::get('category');
            $data->title = Request::get('title');
            $data->save();

            $items = Poster::where('domain_id', $id)
            ->orderBy('category_id', 'asc')
            ->get();

            return response()->json($items);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function posterDel()
    {
        try {
            $data = Poster::find(Request::get('id'));
            $medias = split(', ', $data->media);
            foreach($medias as $media) {
                File::delete(public_path('data/'.$media));
            }
            $data->delete();

            return response()->json(200);
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

            $data = Poster::where('id', Request::get('id'))
            ->with('header.tab.media')->first();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setStatus()
    {
        try {
            $status = Request::get('status');
            $poster = Poster::find(Request::get('id'));

            if ($status == 3) {
                File::delete(public_path('data/'.$poster->media));
                
                $poster->delete();
            } else {
                $poster->status = $status;
                $poster->save();
            }
            $posters = Poster::where('domain_id', Request::get('siteId'))->with('category.backdrop')->orderBy('status', 'desc')->paginate(6);
            return response()->json($posters);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getPreview()
    {
        try {
            $type = Request::get('type');
            if ($type == 2) {
                $poster = Poster::where('id', Request::get('id'))->with('header.tab.media')->with('category.backdrop')->first();
            }
            return response()->json($poster);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
