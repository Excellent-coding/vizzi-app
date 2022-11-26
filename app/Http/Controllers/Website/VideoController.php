<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Request;

use App\User;
use App\Models\VideoPage;
use App\Models\Video;

use App\Models\TrackCategory;
use App\Models\Track;
use App\Models\Page;
use App\Models\Session;
use App\Models\Domain;

class VideoController extends Controller
{
    public function pageget()
    {
        try {
            $id = Request::get('id');

            $data['sessionData'] = Session::where('domain_id', $id)->get();
            $data['domain'] = Domain::where('id', $id)->with('user')->with('admin')->first();
            $data['pageData'] = Page::where('domain_id', $id)->get();
            $data['items'] = Video::where('domain_id', $id)->with('session.event')->get();
            $data['liveCount'] = Video::where('domain_id', $id)->where('format', 0)->count();
            $data['recordedCount'] = Video::where('domain_id', $id)->where('format', 1)->count();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function pageset()
    {
        try {
            $id = Request::get('id');
            $siteId=Request::get('siteId');

            $session_id = Request::get('session_id');
            $title = Request::get('title');
            $format = Request::get('format');
            $kind = Request::get('kind');
            $link = Request::get('link');
            $isChat = Request::get('isChat');
            $isNote = Request::get('isNote');
            $code = Request::get('code');
            $description = Request::get('description');
            $hashtag = Request::get('hashtag');
            $presenter = json_encode(Request::get('presenter'));
            $readMore = Request::get('readMore');

            if ($id == 0) {
                Video::create([
                    'domain_id'     =>  $siteId,
                    'session_id'    =>  $session_id,
                    'title'         =>  $title,
                    'format'        =>  $format,
                    'kind'          =>  $kind,
                    'link'           =>  $link,
                    'isChat'        =>  $isChat,
                    'isNote'        =>  $isNote,
                    'description'   =>  $description,
                    'hashtag'       =>  $hashtag,
                    'presenter'     =>  $presenter,
                    'code'          =>  $code,
                    'readMore'      =>  $readMore
                ]);
            } else {
                $data = Video::find($id);
                $data->domain_id = $siteId;
                $data->session_id = $session_id;
                $data->title = $title;
                $data->link = $link;
                $data->description = $description;
                $data->hashtag = $hashtag;
                $data->presenter = $presenter;
                $data->isChat = $isChat;
                $data->isNote = $isNote;
                $data->code = $code;
                $data->readMore = $readMore;
                $data->save();
            }

            return response()->json(
                Video::where('domain_id', $siteId)->with('session.event')->get()
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function pagedel()
    {
        try {
            Video::find(Request::get('id'))->delete();
            return response()->json(
                Video::where('domain_id', Request::get('siteId'))->with('session')->get()
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function get()
    {
        try {
            $id = Request::get('id');

            $data['domain'] = Domain::where('id', $id)->with('user')->with('admin')->first();
            $data['trackData'] = Track::where('domain_id', $id)->get();
            $data['categoryData'] = TrackCategory::all();
            $data['items'] = VideoPage::where('domain_id', $id)->with('track')->get();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function set()
    {
        try {
            $id = Request::get('id');
            $siteId = Request::get('siteId');

            $date = date('Y/m/d', Request::get('date'));

            if ($id == 0) {
                $item = new VideoPage();
                $strStart = Request::get('start');
                $start = $strStart['hh'].':'.$strStart['mm'].' '.$strStart['A'];
                $start = date('H:i', strtotime($start));
    
                $strEnd = Request::get('end');
                $end = $strEnd['hh'].':'.$strEnd['mm'].' '.$strEnd['A'];
                $end = date('H:i', strtotime($end));
            } else {
                $item = VideoPage::find($id);
                $startStr = explode(':', Request::get('start'));
                $start = $startStr[0].':'.$startStr[1].' '.$startStr[2];
                $start = date('H:i', strtotime($start));
    
                $endStr = explode(':', Request::get('end'));
                $end = $endStr[0].':'.$endStr[1].' '.$endStr[2];
                $end = date('H:i', strtotime($end));
            }
            $item->domain_id = $siteId;
            $item->category_id = Request::get('category_id');
            $item->track_id = Request::get('track_id');
            $item->title = Request::get('title');
            $item->format = Request::get('format');
            $item->kind = Request::get('kind');
            $item->link = Request::get('link');
            $item->isChat = Request::get('isChat');
            $item->isNote = Request::get('isNote');
            $item->description = Request::get('description');
            $item->hashtag = Request::get('hashtag');
            $item->presenter = json_encode(Request::get('presenter'));
            $item->readMore = Request::get('readMore');
            $item->code = Request::get('code');
            $item->start = $start;
            $item->end = $end;
            $item->date = $date;

            $item->save();

            return response()->json(VideoPage::where('domain_id', $siteId)->with('track')->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function del()
    {
        try {
            VideoPage::find(Request::get('id'))->delete();
            return response()->json(
                VideoPage::where('domain_id', Request::get('siteId'))->with('track')->get()
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
