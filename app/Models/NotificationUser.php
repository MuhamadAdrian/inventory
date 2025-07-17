<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'read_at',
    ];

    public function notificaion()
    {
        return $this->belongsTo(Notification::class);
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot('read_at')
            ->withTimestamps();
    }

}