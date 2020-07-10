<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param User $user
     * @return void
     */
    public function store(User $user)
    {
        $message = Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $user->id,
            'content' => request('content')
        ]);
        broadcast(new SendMessage($message))->toOthers();
        auth()->user()->markMessagesSeen($user);
        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Message $message
     * @return Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Message $message
     * @return Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Message $message
     * @return Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Message $message
     * @return Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
