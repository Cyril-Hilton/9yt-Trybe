<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationFollower extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'email',
        'email_notifications',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
