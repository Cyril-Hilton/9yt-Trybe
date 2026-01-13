<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPortfolio extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'title',
        'job_type',
        'portfolio_link',
        'profile_picture',
        'status',
        'rejection_reason',
    ];

    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    public function getProfilePictureUrlAttribute()
    {
        return $this->profile_picture ? asset('storage/' . $this->profile_picture) : null;
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
