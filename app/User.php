<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * @property int id
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function messages_between(User $user)
    {
        return Message::where(['sender_id' => $this->id, 'recipient_id' => $user->id])
            ->orWhere(function ($q) use ($user) {
                $q->where('sender_id', $user->id);
                $q->where('recipient_id', $this->id);
            })
            ->orderBy('id', 'ASC')->get();
    }

    public function markMessagesSeen(User $user)
    {
        DB::table('messages')->where(['recipient_id' => $this->id, 'sender_id' => $user->id, 'seen' => '0'])
            ->update(['seen' => '1']);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

}
