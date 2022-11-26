<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Request;
use File;

use App\Models\Page;
use App\Models\Domain;
use App\Models\Upload;
use App\Models\Booth;
use App\Models\Header;
use App\Models\Poster;
use App\Models\Category;
use App\Models\Session;
use App\Models\Event;
use App\Models\Track;
use App\Models\Hall;
use App\Models\VideoPage;
use App\User;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PageController extends Controller
{
    public function get() {
        $id = Request::get('id');
        $page = Page::find($id);
        $domain = $page->domain()->first();
        if ($page->type == 'content') {
            $titles = [];
            $urls = [];
            foreach (Page::where('domain_id', $domain->id)->get() as $eachPage) {
                array_push($titles, $eachPage->title);
                array_push($urls, $eachPage->url);
            }
            $managers = User::where('domain_id', $domain->id)->where('role', '<', 4)->get();
            return response()->json(compact('page', 'titles', 'urls', 'managers'));
        } else if ($page->type == 'modal') {
            $items = Upload::where('page_id', $id)->orderBy('order', 'asc')->get();

            return response()->json(compact('page', 'items'));
        } else {
            $allPages = Page::where('domain_id', $domain->id)->get();
            return response()->json(compact('page', 'allPages'));
        }
    }

    public function set() {
        try {
            $id = Request::get('id');

            $data = Page::find($id);
    
            $image = Request::file('file');
            if ($image) {
                File::delete(public_path('data/'.$data->background));
                $new_name = rand() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('data/'), $new_name);
                $data->background = $new_name;
            } else {
                $data->background = Request::get('file');
            }

            $originData = explode(', ', $data->media);
            foreach ($originData as $img) {
                File::delete(public_path('data/'.$img));
            }
            
            $media = '';
            $points = json_decode(Request::get('points'));
            foreach($points as $key => $point) {
                $file = Request::file('file'.$key);
                if ($file) {
                    $new_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('data/'), $new_name);
                    $media .= $new_name.', ';
                } else {
                    $media .= $point->media.', ';
                }
            }
            $data->point = $points;
            $data->media = $media;
            
            $data->save();
            
            return response()->json(['success' => true]);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function data() {
        try {
            $domain = Domain::where('domain', Request::get('domain'))->first();
            
            if ($domain) {
                // todo: make any status values constants with meaning
                $headItems = Page::where('domain_id', $domain->id)->where('status', 1)->orderBy('order', 'asc')->with('hall.boothItems.template')->get();
                $totalPages = Page::where('domain_id', $domain->id)->orderBy('order', 'asc')->get();
                $url = Request::get('url');
                $style = $domain->style;
                $logo = $domain->logo;
                $title = $domain->title;
                $video = $domain->video;
                $page = Page::where('domain_id', $domain->id)->where('url', $url)->first();
                if (!$page) {
                    if ($url == '/') {
                        $page = Page::where('domain_id', $domain->id)->where('status', 1)->where('type', 'content')->orderBy('order', 'asc')->first();
                    } else if ($url == '/sponsor' || $url == '/booth' || $url == '/poster') {
    
                    } else {
                        $page = Page::where('domain_id', $domain->id)->where('status', 1)->where('type', 'content')->orderBy('order', 'asc')->first();
                    }
                }

                $modalItems = [];
                foreach($totalPages as $eachPage) {
                    foreach($eachPage->upload()->where('status', 1)->get() as $item) {
                        array_push($modalItems, $item);
                    }
                }
    
                $boothItems = Booth::where('domain_id', $domain->id)->where('type', 0)->get();
                $sponsorItems = Booth::where('domain_id', $domain->id)->where('type', 1)->get();
                $posterItems = Poster::where('domain_id', $domain->id)->get();
    
                $headerItems = Header::all();

                return response()->json(
                    compact(
                        'page', 
                        'logo',
                        'headItems', 
                        'totalPages', 
                        'modalItems', 
                        'style',
                        'boothItems',
                        'sponsorItems',
                        'posterItems',
                        'headerItems',
                        'video'
                    )
                );
            } else {
                return response()->json(['noData' => true]);
            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setIframe()
    {
        try {
            $id = Request::get('id');

            $page = Page::find($id);
            $page->url = Request::get('url');
            $page->title = Request::get('title');
            $page->media = Request::get('media');

            $page->save();
            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function upload() {
        try {
            $editId = Request::get('editId');

            if ($editId == 0) {
                $data = new Upload();
            } else {
                $data = Upload::find($editId);
            }
            $pageId = Request::get('id');
    
            $data->page_id = $pageId;
            $data->title = Request::get('title');
            $data->order = Upload::where('page_id', $pageId)->count();
    
            $file = Request::file('file');
            if ($file) {
                File::delete(public_path('data/'.$data->item));
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                $data->item = $new_name;
            }
    
            $data->save();
    
            return response()->json(Upload::where('page_id', $pageId)->orderBy('order', 'asc')->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function status() {
        try {
            $status = Request::get('status');
            foreach (Request::get('items') as $id) {
                $data = Upload::find($id);
                if ($status != 2) {
                    $data->status = $status;
                    $data->save();
                } else {
                    File::delete(public_path('data/'.$data->file));
                    $data->delete();
                }
            }
            return response()->json(Upload::where('page_id', Request::get('id'))->orderBy('order', 'asc')->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function exhibit() {
        try {
            $page = Page::find(Request::get('id'));
            $page->point = Request::get('menus');

            $file = Request::file('file');
            if ($file) {
                File::delete(public_path('data/'.$page->background));
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                $page->background = $new_name;
            }

            $page->style = Request::get('style');
            
            $page->save();

            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function head()
    {
        try{
            $domain = Domain::where('domain', Request::get('domain'))->with('user')->with('admin')->first();
            $headItems = Page::where('domain_id', $domain->id)->where('status', 1)->orderBy('order', 'asc')->get();
            $style = $domain->style;
            $logo = $domain->logo;
            $title = $domain->title;
            $modalItems = [];

            $pageData = Page::where('domain_id', $domain->id)->orderBy('order', 'asc')->get();
            foreach($pageData as $eachPage) {
                foreach($eachPage->upload()->where('status', 1)->get() as $item) {
                    array_push($modalItems, $item);
                }
            }

            $sessionItems = Session::where('domain_id', $domain->id)->with('track')->with('event')->with('videos')->get();
            $sessionDateData = Event::where('domain_id', $domain->id)->with('session.track')->with('session.event')->get();
            $trackData = Track::where('domain_id', $domain->id)->get();
            $userData = User::select(['id', 'avatar', 'name'])->where('domain_id', $domain->id)->get();
            $videoItems = VideoPage::where('domain_id', $domain->id)->with('track')->get();

            return response()->json(
                compact(
                    'domain',
                    'headItems',
                    'style',
                    'logo',
                    'modalItems',
                    'title',
                    'sessionItems',
                    'title',
                    'sessionDateData',
                    'trackData',
                    'pageData',
                    'userData',
                    'videoItems'
                )
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function booth()
    {
        try {
            $items = Booth::where('domain_id', Request::get('id'))
            ->where('type', 0)
            ->where('status', 2)
            ->with('template')
            ->with('user')
            ->with('header.tab.media')
            ->get();
            return response()->json($items);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function sponsor()
    {
        try {
            $items = Booth::where('domain_id', Request::get('id'))
            ->where('type', 1)
            ->where('status', 2)
            ->with('template')
            ->with('user')
            ->with('header.tab.media')
            ->get();
            return response()->json($items);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function poster()
    {
        try {
            $items = Poster::where('domain_id', Request::get('id'))
            ->with('category.backdrop')
            ->with('header.tab.media')
            ->get();

            return response()->json($items);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function designers()
    {
        try {
            $id = Request::get('id');

            $designers = User::where('domain_id', $id)
            ->where('role', '<', 5)
            ->get();
            $data['designers'] = [];
            foreach($designers as $user) {
                if ($user->avatar != 'default.jpg' && file_exists(public_path('assets/img/avatar/'.$user->avatar))) {
                    $data['designers'][] = $user;
                }
            }

            $adminUsers = Domain::find($id)->user()->get();
            $data['adminUsers'] = [];
            foreach($adminUsers as $user) {
                if ($user->avatar != 'default.jpg' && file_exists(public_path('assets/img/avatar/'.$user->avatar))) {
                    $data['adminUsers'][] = $user;
                }
            }
            
            $superUsers = User::where('role', 1)->get();
            $data['superUsers'] = [];
            foreach($superUsers as $user) {
                if ($user->avatar != 'default.jpg' && file_exists(public_path('assets/img/avatar/'.$user->avatar))) {
                    $data['superUsers'][] = $user;
                }
            }
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function hall()
    {
        try {
            $page = Page::where('url', Request::get('url'))->first();
            $data = Hall::where('id', $page->hall_id)
            ->with('boothItems.template')
            ->with('boothItems.user')
            ->with('boothItems.header.tab.media')
            ->first();

            if ($data->type == 2) {
                $categories = [];
                if ($data->categories) {
                    $categories = json_decode($data->categories);
                }
                $orderOptions = [];
                $categoryOptions = [];
                foreach($categories as $category) {
                    array_push($categoryOptions, $category->title);
                    $cat = Category::where('id', $category->id)->with('posterItems.category.backdrop')->with('posterItems.header.tab.media')->first();
                    foreach($cat->posterItems as $poster) {
                        if ($poster->entry_id) {
                            array_push($orderOptions, substr($poster->entry_id, 1));
                        }
                    }
                }
                sort($categoryOptions);
                sort($orderOptions);
                $optionData = [];
                foreach($orderOptions as $order) {
                    array_push($optionData, 'P'.$order);
                }
                $orderOptions = $optionData;
                array_unshift($categoryOptions, 'All');
                array_unshift($orderOptions, 'All');
                $categories = $this->paginate($categories);
                return response()->json(compact('data', 'categories', 'orderOptions', 'categoryOptions'));
            } else {
                return response()->json($data);
            }
            
            // return response()->json(compact('data', 'categories', 'cateItems', 'headItems', 'posterItems', 'catOptions', 'orderOptions'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getItems()
    {
        try {
            $cat = Category::find(Request::get('id'));
            $data = Poster::where('category_id', $cat->id)->where('status', 1);
            $data = $data->with('category.backdrop')->with('header.tab.media')->get();
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getFilter()
    {
        try {
            $data = Poster::where('domain_id', Request::get('id'))->where('status', 1);
            $filter = Request::get('filter');
            if ($filter['category'] &&  $filter['category'] != 'All') {
                $data = $data->where('category_id', $filter['category']);
            }
            if ($filter['entry_id'] && $filter['entry_id'] != 'All') {
                $data = $data->where('entry_id', $filter['entry_id']);
            }
            if ($filter['rank'] && $filter['rank'] != 'All') {
                if ($filter['rank'] == 'Award') {
                    $data = $data->where('award', 1);
                } else {
                    $data = $data->where('award', 0);
                }
            }
            $data = $data->orderBy('title', 'asc')->with('category.backdrop')->with('header.tab.media')->get();
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function hallData()
    {
        try {
            $data = Hall::where('id', Request::get('id'))
            ->with('boothItems.template')
            ->with('boothItems.user')
            ->with('boothItems.header.tab.media')
            ->first();

            if ($data->type == 2) {
                $categories = [];
                if ($data->categories) {
                    $categories = json_decode($data->categories);
                }
                $categories = $this->paginate($categories);
                return response()->json($categories);
            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }

    }

    public function emailSend()
    {
        try {
            return response()->json(Request::all());
            $items = Request::get('items');
            $text = Request::get('text');
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}