<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = ['name', 'type'];

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }
}
