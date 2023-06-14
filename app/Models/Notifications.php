<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    public $table = 'notifications';
    protected $fillable = [
        'notify_from_user',
        'notify_to_user',
        'notification_type',
        'notify_msg',
        'each_read',
        'status'
    ];
}
