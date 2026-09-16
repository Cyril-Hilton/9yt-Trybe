<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportInboxMessage extends Model
{
    protected $fillable = [
        'message_id', 'from_email', 'from_name', 'subject', 'body',
        'classification', 'status', 'reply_body', 'received_at', 'replied_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'replied_at' => 'datetime',
    ];
}
