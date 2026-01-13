<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationPaymentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'account_type',
        'bank_name',
        'account_name',
        'account_number',
        'branch',
        'swift_code',
        'mobile_money_network',
        'mobile_money_number',
        'mobile_money_name',
        'is_verified',
        'verified_at',
        'is_default',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_default' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function payouts()
    {
        return $this->hasMany(EventPayout::class, 'payment_account_id');
    }

    public function isBankAccount(): bool
    {
        return $this->account_type === 'bank';
    }

    public function isMobileMoney(): bool
    {
        return $this->account_type === 'mobile_money';
    }

    public function getDisplayNameAttribute()
    {
        if ($this->isBankAccount()) {
            return $this->bank_name . ' - ' . $this->account_number;
        }

        if ($this->isMobileMoney()) {
            return $this->mobile_money_network . ' - ' . $this->mobile_money_number;
        }

        return 'Unknown Account';
    }

    // Ensure only one default account per company
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($account) {
            if ($account->is_default) {
                static::where('company_id', $account->company_id)
                    ->where('id', '!=', $account->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
