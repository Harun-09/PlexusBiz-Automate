<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormSubmission extends Model
{
    use HasFactory;

    protected $fillable = ['custom_form_id', 'user_id', 'ip_address'];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id');
    }

    public function values()
    {
        return $this->hasMany(CustomFormSubmissionValue::class);
    }
}
