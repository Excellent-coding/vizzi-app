<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollAnswers;
use App\Http\Resources\Poll as PollResource;

class PollController extends Controller
{
    public function getAll() {
        $polls = Poll::get();
        return response()->json(['data' => PollResource::collection($polls)]);
    }

    public function getOne(Poll $poll) {
        return response()->json(['data' => new PollResource($poll)]);
    }

    public function create(Request $request) {
        $poll = Poll::create($request->all());
        return response()->json(['data' => $poll]);
    }

    public function update(Poll $poll, Request $request) {
        if ($request->has('title')) {
            $poll->title = $request->title;
        }

        if ($request->has('answers')) {
            $poll->answers = $request->answers;
        }

        if ($request->has('expire')) {
            $poll->expire = $request->expire;
        }

        if ($request->has('active')) {
            $poll->active = $request->active;
        }
        $poll->save();

        return response()->json(['data' => $poll]);
    }

    public function delete(Poll $poll) {
        $poll->delete();
        return response()->json(['data' => null, 'message' => 'Successfuly deleted.']);
    }

    public function getAvailable(Request $request) {
        $polls = Poll::where('active', true)->where('expire', '>', now())->get();
        return response()->json(['data' => PollResource::collection($polls)]);
    }
    
    public function addAnswer(Poll $poll, Request $request) {
        $user = auth()->user();
        $poll->answers()->create([
            'user_id' => $user->id,
            'answer' => $request->answer
        ]);
        return response()->json(['data' => null, 'message' => 'Successfuly answered.']);
    }

    public function getStats(Request $request) {
        $ids = $request->ids;
        if ($ids) {
            $answers = PollAnswers::whereIn('poll_id', $ids)->get()->groupBy('poll_id');
            $stat = $answers->map(function($answer) {
                return [
                    'total' => $answer->count(),
                    'answers' => $answer->groupBy('answer')->map(function($item) {
                        return $item->count();
                    })
                ];
            });

            return response()->json(['data' => $stat]);
        }
    }
}
