<?php

namespace App\Domains\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BuyerOrganization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'registration_number',
        'industry',
        'tax_id',
        'billing_address',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
