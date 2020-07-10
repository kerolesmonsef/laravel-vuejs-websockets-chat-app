<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth = auth()->user();
        $users = (new User)->newQuery();
        $users->select('users.*',
            DB::raw("(SELECT count(messages.id)
                        FROM messages
                        WHERE messages.sender_id = users.id
                          AND messages.recipient_id = $auth->id
                          AND messages.seen = '0' ) as unseen_msg_cnt"))
            ->where('users.id', '<>', auth()->id());

        return response()->json($users->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * get Messages between auth and user
     * @param User $user
     * @return JsonResponse
     */
    public function messages_between(User $user)
    {
        $messages = auth()->user()->messages_between($user);
        return response()->json($messages);
    }

    /**
     * make the income messages seen
     *
     * @param User $user
     * @return JsonResponse
     */
    public function markMessagesSeen(User $user)
    {
        auth()->user()->markMessagesSeen($user);
        return response()->json([
            'seen' => '1'
        ]);
    }

    /**
     * get the number of unseen message between auth and user
     *
     * @param User $user
     * @return
     */
    public function unseenMessagesCount(User $user)
    {
        $mCount = Message::where(['recipient_id' => auth()->id(), 'sender_id' => $user->id, 'seen' => '0'])
            ->count();
        return response()->json([
            'mCount' => $mCount
        ]);
    }
}
