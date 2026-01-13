<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_order_id',
        'event_ticket_id',
        'event_id',
        'attendee_name',
        'attendee_email',
        'attendee_phone',
        'ticket_code',
        'qr_code_path',
        'price_paid',
        'checked_in',
        'checked_in_at',
        'checked_in_by',
        'status',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(EventOrder::class, 'event_order_id');
    }

    public function ticket()
    {
        return $this->belongsTo(EventTicket::class, 'event_ticket_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function isValid(): bool
    {
        return $this->status === 'valid';
    }

    public function isCheckedIn(): bool
    {
        return $this->checked_in;
    }

    public function checkIn($userId = null)
    {
        $this->update([
            'checked_in' => true,
            'checked_in_at' => now(),
            'checked_in_by' => $userId,
        ]);
    }

    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return null;
    }

    public function getQrCodeBase64Attribute()
    {
        // Prefer PNG generation for broad email client support.
        if ($this->ticket_code && extension_loaded('imagick')) {
            try {
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(200)
                    ->margin(1)
                    ->errorCorrection('M')
                    ->generate($this->ticket_code);

                $imageData = base64_encode($qrCode);
                return 'data:image/png;base64,' . $imageData;
            } catch (\Exception $e) {
                \Log::error('QR code generation failed: ' . $e->getMessage());
            }
        }

        // Fall back to stored QR code if PNG generation fails.
        if ($this->qr_code_path) {
            $path = storage_path('app/public/' . $this->qr_code_path);
            if (file_exists($path)) {
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mimeType = $extension === 'svg' ? 'image/svg+xml' : 'image/png';
                $imageData = base64_encode(file_get_contents($path));
                return "data:{$mimeType};base64,{$imageData}";
            }
        }

        return null;
    }
}
