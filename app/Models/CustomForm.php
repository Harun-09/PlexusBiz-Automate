<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomForm extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'is_active', 'target_role'];

    public function fields()
    {
        return $this->hasMany(CustomFormField::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(CustomFormSubmission::class);
    }
}
