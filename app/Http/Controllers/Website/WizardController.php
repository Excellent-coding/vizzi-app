<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Request;
use Validator;
use File;

use App\Models\Domain;
use App\Models\Booth;
use App\Models\Hall;
use App\Models\Template;
use App\Models\Header;
use App\Models\Category;
use App\Models\Poster;
use App\Models\PosterBackdrop;
use App\User;

use App\Notifications\RegisterNotification;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class WizardController extends Controller
{
    public function get()
    {
        try {
            $id = Request::get('id');
            $type = Request::get('type');

            if (auth()->user()->role == 3) {
                $this->bladeContent['users'] = User::where('type', $type)->where('parent', auth()->user()->id)->where('domain_id', $id)->get();
                if ($type < 2) {
                    $this->bladeContent['booths'] = Booth::where('user_id', auth()->user()->id)->where('type', $type)->where('domain_id', $id)->get();
                } else {
                    $this->bladeContent['booths'] = Poster::where('user_id', auth()->user()->id)->where('domain_id', $id)->get();
                }
            } else if (auth()->user()->role == 4) {
                $booths = Booth::where('user_id', auth()->user()->id)->where('type', $type)->where('domain_id', $id)->get();
                if ($booths->count() == 0) {
                    $booths = Booth::where('user_id', auth()->user()->adult->id)->where('type', $type)->where('domain_id', $id)->get();
                }
                $this->bladeContent['booths'] = $booths;
            } else {
                $this->bladeContent['users'] = User::where('type', $type)->where('domain_id', $id)->get();
                if ($type < 2) {
                    $this->bladeContent['booths'] = Booth::where('type', $type)->where('domain_id', $id)->get();
                } else {
                    $this->bladeContent['booths'] = Poster::where('domain_id', $id)->get();
                }
            }

            $this->bladeContent['categories'] = Category::where('domain_id', $id)->get();
            $this->bladeContent['domain'] = Domain::find($id);

            return response()->json($this->bladeContent);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function upload()
    {
        try {
            $id = Request::get('siteId');
            $type = Request::get('type');
            $editId = Request::get('id');

            if ($editId == 0) {
                $data = new Template();
            } else {
                $data = Template::find($editId);
            }

            $data->domain_id = $id;
            $data->user_id = auth()->user()->id;

            $file = Request::file('file');
            if ($file) {
                File::delete(public_path('data/'.$data->media));
                $new_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('data/'), $new_name);
                $data->media = $new_name;
            }

            $data->type = $type;
            $data->save();

            if ($type < 2) {
                $models = Template::where('domain_id', $id)
                ->where('type', $type)
                ->get();
                return response()->json($models);
            } else {
                $models = Category::where('domain_id', $id)
                ->orderBy('order', 'asc')
                ->with('backdrop')
                ->paginate(4);
                $backdrops = PosterBackdrop::where('domain_id', $id)->get();
                return response()->json(compact('models', 'backdrops'));
            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function models()
    {
        try {
            $type = Request::get('type');
            $id = Request::get('siteId');
            if ($type < 2) {
                $models = Template::where('domain_id', $id)
                ->where('type', $type)
                ->get();
                return response()->json($models);
            } else {
                $models = Category::where('domain_id', $id)
                ->orderBy('order', 'desc')
                ->with('backdrop')
                ->paginate(4);
                $dropData = PosterBackdrop::where('domain_id', $id);
                if ($dropData->count() > 3) {
                    foreach($dropData->get() as $key => $data) {
                        if ($key > 2) {
                            File::delete(public_path('data/'.$data->media));
                            $data->delete();
                        }
                    }
                }
                $backdrops = PosterBackdrop::where('domain_id', $id)->get();
                return response()->json(compact('models', 'backdrops'));
            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getModels()
    {
        try {
            $models = Category::where('domain_id', Request::get('id'))
            ->orderBy('order', 'desc')
            ->with('backdrop')
            ->paginate(4);
            return response()->json($models);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function member()
    {
        try {
            $memberId = Request::get('memberId');
            $member = Request::get('member');
            $id = Request::get('id');
            $type = Request::get('type');
            $title = Request::get('booth');

            if ($memberId == 0) {
                $validation = Validator::make($member, [
                    'name' => 'max:255',
                    'email' => 'email|max:255|unique:users'
                ]);
    
                if (!$validation->passes()) {
                    return response()->json([
                        'errors' => $validation->errors()
                    ]);
                }
            }

            if ($type < 2) {
                $count = Booth::where('title', $title)->count();
                if ($count) {
                    $booth = Booth::where('title', $title)->first();
                } else {
                    $booth = Booth::create([
                        'domain_id' =>  $id,
                        'title'     =>  $title,
                        'user_id'   =>  auth()->user()->id,
                        'type'      =>  $type
                    ]);
                }
            } else {
                $count = Poster::where('title', $title)->count();
                if ($count) {
                    $booth = Poster::where('title', $title)->first();
                } else {
                    $booth = Poster::create([
                        'domain_id' =>  $id,
                        'title'     =>  $title,
                        'user_id'   =>  auth()->user()->id,
                        'type'      =>  $type
                    ]);
                }
            }

            if ($memberId == 0) {
                $role = auth()->user()->role + 1;
                $user = User::create([
                    'name'  => $member['name'],
                    'email'  => $member['email'],
                    'password'  => bcrypt('secret'),
                    'parent'    => auth()->user()->id,
                    'type'  => $type,
                    'role'  => $role,
                    'domain_id' => $id,
                    'booth_id' => $booth->id,
                ]);
            } else {
                $user = User::find($memberId);
                $user->name = $member['name'];
                $user->email = $member['email'];
                $user->password = bcrypt('secret');
                $user->save();
            }

            $this->bladeContent['members'] = User::where('parent', auth()->user()->id)
            ->where('type', $type)
            ->where('booth_id', $booth->id)
            ->get();

            if ($type < 2) {
                if (auth()->user()->role == 3) {
                    $this->bladeContent['booths'] = Booth::where('user_id', auth()->user()->id)->where('type', $type)->where('domain_id', $id)->get();
                } else {
                    $this->bladeContent['booths'] = Booth::where('type', $type)->where('domain_id', $id)->get();
                }
            } else {
                if (auth()->user()->role == 3) {
                    $this->bladeContent['booths'] = Poster::where('user_id', auth()->user()->id)->where('domain_id', $id)->get();
                } else {
                    $this->bladeContent['booths'] = Poster::where('domain_id', $id)->get();
                }
            }

            return response()->json($this->bladeContent);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function del()
    {
        try {
            $user = User::find(Request::get('id'));
            if ($user) {
                $user->delete();
            }
            $type = Request::get('type');
            $id = Request::get('siteId');

            if (auth()->user()->role == 3) {
                $users = User::where('type', $type)->where('parent', auth()->user()->id)->where('domain_id', $id)->get();
            } else {
                $users = User::where('type', $type)->where('domain_id', $id)->get();
            }

            return response()->json($users);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setBooth()
    {
        try {
            $title = Request::get('title');
            $id = Request::get('id');
            $type = Request::get('type');
            if ($type < 2) {
                $count = Booth::where('title', $title)->where('type', $type)->count();
                if ($count) {
                    $booth = Booth::where('title', $title)->where('type', $type)->first();
                } else {
                    $booth = new Booth();
                }
            } else {
                $count = Poster::where('title', $title)->count();
                if ($count) {
                    $booth = Poster::where('title', $title)->first();
                } else {
                    $booth = new Poster();
                }
                $booth->category_id = Request::get('category');
            }
            
            $booth->domain_id = $id;
            $booth->title = $title;
            $booth->user_id = auth()->user()->id;
            $booth->type = $type;
            $booth->save();

            $members = Request::get('members');
            foreach ($members as $member) {
                $validation = Validator::make($member, [
                    'name' => 'max:255',
                    'email' => 'email|max:255|unique:users'
                ]);
                
                if ($validation->passes()) {
                    $role = auth()->user()->role + 1;
                    User::create([
                        'name'  => $member['name'],
                        'email'  => $member['email'],
                        'password'  => bcrypt('secret'),
                        'parent'    => auth()->user()->id,
                        'type'  => $type,
                        'role'  => $role,
                        'domain_id' => $id,
                        'booth_id' => $booth->id,
                    ]);
                }
            }
    
            $custom = Request::get('custom');
            if ($custom) {
                $booth->template_id = null;
                $booth->save();
            }
            return response()->json($booth);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function delTemp()
    {
        try {
            $data = Template::find(Request::get('id'));
            File::delete(public_path('data/'.$data->media));
            $data->delete();

            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setTemp()
    {
        try {
            $data = Booth::find(Request::get('id'));
            $data->template_id = Request::get('temp_id');
            $data->save();
            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $id = Request::get('id');
            $type = Request::get('type');

            $hallData = Hall::where('domain_id', $id)->where('type', $type)->with('boothItems.template')->get();
            if (auth()->user()->role < 3) {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', '>', 1)->get();
                return response()->json(compact('workingItems', 'activeItems', 'pendingItems', 'hallData'));
            } else {
                $workingItems = Booth::with('user')->with('template')->find(auth()->user()->booth_id);
                return response()->json($workingItems);
            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function approve()
    {
        try {
            $booth = Booth::find(Request::get('id'));
            $booth->status = 2;
            $booth->save();

            $id = Request::get('siteId');
            $type = Request::get('type');

            if (auth()->user()->role < 3) {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', '>', 1)->get();
            } else {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', '>', 1)->get();
            }

            return response()->json(compact('workingItems', 'activeItems', 'pendingItems'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function reject()
    {
        try {
            $booth = Booth::find(Request::get('id'));
            $booth->status = 0;
            $booth->save();

            $id = Request::get('siteId');
            $type = Request::get('type');

            if (auth()->user()->role < 3) {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', '>', 1)->get();
            } else {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', '>', 1)->get();
            }

            return response()->json(compact('workingItems', 'activeItems', 'pendingItems'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function activate()
    {
        try {
            $booth = Booth::find(Request::get('id'));
            $booth->status = 2;
            $booth->save();

            $id = Request::get('siteId');
            $type = Request::get('type');

            if (auth()->user()->role < 3) {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', '>', 1)->get();
            } else {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', '>', 1)->get();
            }

            return response()->json(compact('workingItems', 'activeItems', 'pendingItems'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function remove()
    {
        try {
            $booth = Booth::find(Request::get('id'))->delete();

            $id = Request::get('siteId');
            $type = Request::get('type');

            if (auth()->user()->role < 3) {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', '>', 1)->get();
            } else {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', '>', 1)->get();
            }

            return response()->json(compact('workingItems', 'activeItems', 'pendingItems'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function deactivate()
    {
        try {
            $booth = Booth::find(Request::get('id'));
            $booth->status = 3;
            $booth->save();

            $id = Request::get('siteId');
            $type = Request::get('type');

            if (auth()->user()->role < 3) {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('status', '>', 1)->get();
            } else {
                $workingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 0)->get();
                $pendingItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', 1)->get();
                $activeItems = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('template')->where('user_id', auth()->user()->id)->where('status', '>', 1)->get();
            }

            return response()->json(compact('workingItems', 'activeItems', 'pendingItems'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getBoothData()
    {
        try {
            return response()->json(
                Booth::where('domain_id', Request::get('id'))->where('type', Request::get('type'))->with('user')->with('members')->get()
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getPosterData()
    {
        try {
            return response()->json(
                Poster::where('domain_id', Request::get('id'))->with('user')->with('category')->paginate(8)
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getPosterUser()
    {
        try {
            return response()->json(
                Poster::where('domain_id', Request::get('id'))->with('user')->with('category')->paginate(8)
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function boothStatus()
    {
        try {
            $booth = Booth::find(Request::get('id'));
            $booth->status = Request::get('status');
            $booth->save();

            return response()->json(
                Booth::where('domain_id', Request::get('siteId'))
                ->where('type', Request::get('type'))
                ->with('user')
                ->with('members')
                ->get()
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function posterStatus()
    {
        try {
            $poster = Poster::find(Request::get('id'));

            $status = Request::get('status');

            if ($status != 2) {
                $poster->status = Request::get('status');
                $poster->save();
            } else {
                $poster->delete();
            }
            
            return response()->json(
                Poster::where('domain_id', Request::get('siteId'))
                ->orderBy('status', 'desc')
                ->with('user')
                ->with('category')
                ->get()
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function userStatus()
    {
        try {
            $user = User::find(Request::get('id'));
            $user->status = Request::get('status');
            $user->save();

            return response()->json(
                Booth::where('domain_id', Request::get('siteId'))
                ->where('type', Request::get('type'))
                ->with('user')
                ->with('members')
                ->get()
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getSetting()
    {
        try {
            $id = Request::get('id');
            $type = Request::get('type');

            $domain = Domain::find($id);

            $title = 'title'.$type;
            $content = 'content'.$type;
            $note = 'note'.$type;

            $title = $domain->$title;
            $content = $domain->$content;
            $note = $domain->$note;
            $modal = $domain->modal;

            return response()->json(
                compact('title', 'content', 'note', 'modal')
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setText()
    {
        try {
            $domain = Domain::find(Request::get('id'));
            $type = Request::get('type');

            $title = 'title'.$type;
            $content = 'content'.$type;

            $domain->$title = Request::get('title');
            $domain->$content = Request::get('content');

            $domain->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function note()
    {
        try {
            $domain = Domain::find(Request::get('id'));
            $type = Request::get('type');

            $note = 'note'.$type;

            $domain->$note = Request::get('note');

            $domain->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    
    public function posterUpload()
    {
        try {
            $id = Request::get('id');
            $categoryData = Request::get('categoryData');
            $userData = Request::get('userData');
            $posterData = Request::get('posterData');

            foreach($userData as $key => $member) {
                $validation = Validator::make($member, [
                    'name' => 'max:255',
                    'email' => 'email|max:255|unique:users',
                ]);

                $code = rand(1000, 9999);

                if ($validation->passes()) {
                    if (auth()->user()->role < 3) {
                        $role = 3;
                    } else {
                        $role = 4;
                    }
                    $user = User::create([
                        'name'  => $member['name'],
                        'email'  => $member['email'],
                        'verify_code' => $code,
                        'parent'    => auth()->user()->id,
                        'type'  => 2,
                        'role'  => $role,
                        'domain_id' => $id,
                    ]);

                    $user->notify(new RegisterNotification($code, null));
                } else {
                    $error = $validation->errors();
                    $user = User::where('email', $member['email'])->first();
                    if (!empty($user)) {
                        if (empty($user->password)) {
                            $user->notify(new RegisterNotification($user->verify_code, null));
                        }
                    } else {
                        return response()->json(['errors' => $error]);
                    }
                }
                
                $categoryValidation = Validator::make($categoryData[$key], [
                    'category' => 'max:255|unique:categories',
                ]);
                
                if ($categoryValidation->passes()) {
                    $category = Category::create([
                        'domain_id' =>  $id,
                        'user_id'   =>  auth()->user()->id,
                        'category'      =>  $categoryData[$key]['category']
                    ]);
                } else {
                    $error = $categoryValidation->errors()->get('category');
                    if (count($error) == 1 && $error[0] == 'The category has already been taken.') {
                        $category = Category::where('category', $categoryData[$key]['category'])->first();
                    } else {
                        return response()->json(['errors' => $error]);
                    }
                }
                
                $posterValidation = Validator::make($posterData[$key], [
                    'entry_id' => 'unique:posters',
                ]);

                if ($posterValidation->passes()) {
                    $poster = Poster::create([
                        'domain_id'     =>  $id,
                        'category_id'   =>  $category->id,
                        'title'         =>  $posterData[$key]['title'],
                        'user_id'       =>  $user->id,
                        'entry_id'      =>  $posterData[$key]['entry_id']
                    ]);
                } else {
                    $poster = Poster::where('entry_id', $posterData[$key]['entry_id'])->first();
                    $poster->user_id = $user->id;
                    $poster->save();
                }

                $user->type = 2;
                $user->booth_id = $poster->id;
                $user->save();
            }
            $posters = Poster::where('domain_id', $id)->with('user')->with('category')->get();
            return response()->json($posters);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function exhibitUpload()
    {
        try {
            $id = Request::get('id');
            $boothData = Request::get('boothData');
            $userData = Request::get('userData');
            $type = Request::get('type');

            foreach($userData as $key => $member) {
                $validation = Validator::make($member, [
                    'name' => 'max:255',
                    'email' => 'email|max:255|unique:users',
                ]);

                $code = rand(1000, 9999);
                
                if ($validation->passes()) {
                    if (auth()->user()->role < 3) {
                        $role = 3;
                    } else {
                        $role = 4;
                    }
                    $user = User::create([
                        'name'  => $member['name'],
                        'email'  => $member['email'],
                        'password'  => bcrypt('secret'),
                        'verify_code' => $code,
                        'parent'    => auth()->user()->id,
                        'type'  => $type,
                        'role'  => $role,
                        'domain_id' => $id,
                    ]);

                    $user->notify(new RegisterNotification($code, null));
                } else {
                    $error = $validation->errors()->get('email');
                    if (count($error) == 1 && $error[0] == 'The email has already been taken.') {
                        $user = User::where('email', $member['email'])->first();
                        $user->type = $type;
                        $user->save();
                    } else {
                        return response()->json(['errors' => $error]);
                    }
                }

                $boothValidation = Validator::make($boothData[$key], [
                    'title' => 'max:255|unique:booths',
                ]);

                if ($boothValidation->passes()) {
                    $booth = Booth::create([
                        'domain_id' =>  $id,
                        'user_id'   =>  $user->id,
                        'title'      =>  $boothData[$key]['title'],
                        'type'  => $type
                    ]);
                } else {
                    $error = $boothValidation->errors()->get('title');
                    if (count($error) == 1 && $error[0] == 'The title has already been taken.') {
                        $booth = Booth::where('title', $boothData[$key]['title'])->first();
                        $booth->user_id = $user->id;
                        $booth->type = $type;
                        $booth->save();
                    } else {
                        return response()->json(['errors' => $error]);
                    }
                }

                $user->booth_id = $booth->id;
                $user->save();
            }

            $booths = Booth::where('domain_id', $id)->where('type', $type)->with('user')->with('members')->get();

            return response()->json($booths);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function categoryStatus()
    {
        try {
            $category = Category::find(Request::get('id'));
            $category->status = Request::get('status');
            $category->save();

            $models = Category::where('domain_id', Request::get('siteId'))->get();
            return response()->json($models);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setBackdrop()
    {
        try {
            $id = Request::get('siteId');
            for ($i = 1; $i < 4; $i++) {
                $data = new PosterBackdrop();
                $file = Request::file('file'.$i);
                if ($file) {
                    $new_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('assets/img/poster-backdrop'), $new_name);
                    $data->media = $new_name;
                    $data->user_id = auth()->user()->id;
                    $data->domain_id = $id;
                    $data->save();
                }
            }

            $backdrops = PosterBackdrop::where('domain_id', $id)->get();

            return response()->json($backdrops);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function editBackdrop()
    {
        try {
            $id = Request::get('siteId');
            $dataId = Request::get('id');
            for ($i = 0; $i < 3; $i++) {
                $data = PosterBackdrop::find($dataId);
                $file = Request::file('image'.$i);
                if ($file) {
                    File::delete(public_path('data/'.$data->media));
                    $new_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('assets/img/poster-backdrop'), $new_name);
                    $data->media = $new_name;
                    $data->user_id = auth()->user()->id;
                    $data->domain_id = $id;
                    $data->save();
                }
            }

            $backdrops = PosterBackdrop::where('domain_id', $id)->get();

            return response()->json($backdrops);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setType()
    {
        try {
            $data = Category::find(Request::get('id'));
            $data->bg = Request::get('backdropId');
            $data->save();

            $models = Category::where('domain_id', Request::get('siteId'))
            ->orderBy('order', 'asc')
            ->with('backdrop')
            ->paginate(4);
            return response()->json($models);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getTemp()
    {
        try {
            return response()->json(
                $models = Category::where('domain_id', Request::get('siteId'))
                ->orderBy('order', 'asc')
                ->with('backdrop')
                ->paginate(4)
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function delCat()
    {
        try {
            Category::find(Request::get('id'))->delete();
            return response()->json(
                $models = Category::where('domain_id', Request::get('siteId'))
                ->orderBy('order', 'asc')
                ->with('backdrop')
                ->paginate(4)
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getPoster()
    {
        try {
            $siteId = Request::get('siteId');
            if (auth()->user()->role < 3) {
                $posters = Poster::where('domain_id', $siteId)->with('category.backdrop')->orderBy('status', 'desc');
            } else {
                $posters = Poster::where('domain_id', $siteId)->where('user_id', auth()->user()->id)->with('category.backdrop')->orderBy('status', 'desc');
            }
            $filter = Request::get('filter');
            if ($filter['category'] && $filter['category'] != 0) {
                $posters = $posters->where('category_id', $filter['category']);
            }
            if ($filter['entry_id'] && $filter['entry_id'] != 'all') {
                $posters = $posters->where('entry_id', $filter['entry_id']);
            }
            if ($filter['rank'] && $filter['rank'] != 'All') {
                if ($filter['rank'] == 'Award') {
                    $posters = $posters->where('award', 1);
                } else {
                    $posters = $posters->where('award', 0);
                }
            }
            $posters = $posters->with('header.tab.media')->paginate(6);
            $hallData = Hall::where('domain_id', $siteId)
            ->where('type', 2)
            ->get();

            $categoryData = Category::where('domain_id', $siteId)->where('status', 1)->get();
            $entryData = [];
            foreach(Poster::where('domain_id', $siteId)->get() as $entry) {
                if (!is_null($entry->entry_id)) {
                    array_push($entryData, $entry->entry_id);
                }
            }
            $entryData = array_unique($entryData);

            return response()->json(compact('posters', 'hallData', 'categoryData', 'entryData'));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getPosterPart() {
        try {
            $id = Request::get('id');
            if (auth()->user()->role < 3) {
                $posters = Poster::where('domain_id', $id)->with('category.backdrop')->with('header.tab.media')->orderBy('status', 'desc')->paginate(6);
            } else {
                $posters = Poster::where('domain_id', $id)->where('user_id', auth()->user()->id)->with('category.backdrop')->with('header.tab.media')->orderBy('status', 'desc')->paginate(6);
            }
            return response()->json($posters);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setCat()
    {
        try {
            $id = Request::get('id');

            $catId = Request::get('categoryId');
            if ($catId == 0) {
                $data = new Category();
            } else {
                $data = Category::find($catId);
            }

            $data->category = Request::get('category');
            $data->domain_id = $id;
            $data->user_id = auth()->user()->id;
            $data->order = Request::get('order');
            $data->save();

            return response()->json(
                Category::where('domain_id', $id)
                ->orderBy('order', 'desc')
                ->with('backdrop')
                ->paginate(4)
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setPosterCat()
    {
        try {
            $data = Poster::find(Request::get('id'));
            $data->category_id = Request::get('cat_id');
            $data->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function setContact()
    {
        try {
            $contact = 'contact'.Request::get('type');
            $data = Domain::find(Request::get('id'));
            $data->$contact = Request::get('status');
            $data->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function hallSet()
    {
        try {
            $id = Request::get('id');
            $siteId = Request::get('siteId');
            $name = Request::get('name');
            $type = Request::get('type');

            if ($type < 2) {
                if ($id == 0) {
                    $hall = Hall::create([
                        'domain_id' =>  $siteId,
                        'type'      =>  $type,
                        'name'      =>  $name,
                        'user_id'   =>  auth()->user()->id
                    ]);
                } else {
                    $hall = Hall::find($id);
                    $hall->name = $name;
                    $hall->type = $type;
                    $hall->user_id = auth()->user()->id;
                    $hall->domain_id = $siteId;
                    $hall->save();
    
                    foreach(Booth::where('hall_id', $hall->id)->get() as $data) {
                        $data->hall_id = null;
                        $data->save();
                    }
                }
    
                $list = Request::get('list');
                foreach($list as $id) {
                    $item = Booth::find($id);
                    $item->hall_id = $hall->id;
                    $item->save();
                }
    
                $hallData = Hall::where('domain_id', $siteId)
                ->where('type', $type)
                ->with('boothItems.template')
                ->get();
            } else {
                $list = Request::get('list');
                if ($list) {
                    $list = json_encode($list);
                }
                if ($id == 0) {
                    $hall = Hall::create([
                        'domain_id' =>  $siteId,
                        'type'      =>  $type,
                        'name'      =>  $name,
                        'user_id'   =>  auth()->user()->id,
                        'categories'=>  $list
                    ]);
                } else {
                    $hall = Hall::find($id);
                    $hall->name = $name;
                    $hall->type = $type;
                    $hall->user_id = auth()->user()->id;
                    $hall->domain_id = $siteId;
                    $hall->categories = $list;
                    $hall->save();
                }

                $hallData = Hall::where('domain_id', $siteId)
                ->where('type', $type)
                ->get();
            }

            return response()->json($hallData);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function hallItemDel()
    {
        try {
            $type = Request::get('type');

            if ($type < 2) {
                $data = Booth::find(Request::get('id'));
            } else {
                $data = Poster::find(Request::get('id'));
            }
            $data->hall_id = null;
            $data->save();

            if ($type < 2) {
                $hallData = Hall::where('domain_id', Request::get('siteId'))
                ->where('type', $type)
                ->with('boothItems.template')
                ->get();
            } else {
                $hallData = Hall::where('domain_id', Request::get('siteId'))
                ->where('type', 2)
                ->get();
            }

            return response()->json($hallData);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function hallStatus()
    {
        try {
            $status = Request::get('status');
            $id = Request::get('id');

            $type = Request::get('type');

            if ($status == 2) {
                if ($type < 2) {
                    foreach (Booth::where('hall_id', $id)->get() as $data) {
                        $data->hall_id = null;
                        $data->save();
                    }
                }
                Hall::find($id)->delete();
            } else {
                $data = Hall::find($id);
                $data->status = $status;
                $data->save();
            }

            if ($type < 2) {
                $hallData = Hall::where('domain_id', Request::get('siteId'))
                ->where('type', $type)
                ->with('boothItems.template')
                ->get();
            } else {
                $hallData = Hall::where('domain_id', Request::get('siteId'))
                ->where('type', 2)
                ->get();
            }
            return response()->json($hallData);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function modalSet()
    {
        try {
            $domain = Domain::find(Request::get('id'));
            $domain->modal = Request::get('modal');
            $domain->save();
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function award()
    {
        try {
            $type = Request::get('type');
            if ($type == 2) {
                $poster = Poster::find(Request::get('id'));
                $poster->award = !$poster->award;
                $poster->save();

                $data = Poster::where('domain_id', Request::get('siteId'))->with('category.backdrop')->orderBy('status', 'desc');
                $filter = Request::get('filter');

                if (isset($filter['category'])) {
                    if ($filter['category'] != 0) {
                        $data = $data->where('category_id', $filter['category']);
                    }
                }

                if (isset($filter['entry_id'])) {
                    if ($filter['entry_id'] != 'all') {
                        $data = $data->where('entry_id', $filter['entry_id']);
                    }
                }

                if (isset($filter['rank'])) {
                    if ($filter['rank'] != 'All') {
                        if ($filter['rank'] == 'Award') {
                            $data = $data->where('award', 1);
                        } else {
                            $data = $data->where('award', 0);
                        }
                    }
                }

                $data = $data->with('header.tab.media')->paginate(6);
            }
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
