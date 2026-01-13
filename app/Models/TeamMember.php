<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'full_name',
        'title',
        'role',
        'job_description',
        'portfolio_link',
        'contact_number',
        'socials',
        'email',
        'status',
        'rejection_reason',
    ];

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
