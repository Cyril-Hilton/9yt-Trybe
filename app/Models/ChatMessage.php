<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewChatMessage;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'session_id',
        'name',
        'email',
        'message',
        'admin_reply',
        'replied_by',
        'replied_at',
        'is_read',
        'status',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function repliedByAdmin()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    // Helper methods
    public function getSenderNameAttribute()
    {
        if ($this->user_id) {
            return $this->user->name;
        } elseif ($this->company_id) {
            return $this->company->name . ' (Organizer)';
        } else {
            return $this->name ?? 'Guest';
        }
    }

    public function getSenderEmailAttribute()
    {
        if ($this->user_id) {
            return $this->user->email;
        } elseif ($this->company_id) {
            return $this->company->email;
        } else {
            return $this->email;
        }
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function reply($adminUserId, $replyMessage)
    {
        $this->update([
            'admin_reply' => $replyMessage,
            'replied_by' => $adminUserId,
            'replied_at' => now(),
            'status' => 'replied',
            'is_read' => true,
        ]);

        // Send email notification to user
        if ($this->sender_email) {
            try {
                Mail::to($this->sender_email)->send(new \App\Mail\ChatReplyNotification($this));
            } catch (\Exception $e) {
                \Log::error('Failed to send chat reply notification: ' . $e->getMessage());
            }
        }
    }

    // Send email notification to admin when new message arrives
    public static function notifyAdmin($chatMessage)
    {
        // Get admin email from config or env
        $adminEmail = config('mail.from.address', '9yttrybe@gmail.com');

        try {
            Mail::to($adminEmail)->send(new NewChatMessage($chatMessage));
        } catch (\Exception $e) {
            \Log::error('Failed to send admin notification: ' . $e->getMessage());
        }
    }
}
