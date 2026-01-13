<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sms_credits',
        'price',
        'badge',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sms_credits' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(SmsTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sms_credits');
    }
}
