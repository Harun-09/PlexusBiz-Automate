<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormField extends Model
{
    use HasFactory;

    protected $fillable = ['custom_form_id', 'label', 'name', 'type', 'is_required', 'options', 'order'];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id');
    }
}
