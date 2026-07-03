<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormSubmissionValue extends Model
{
    use HasFactory;

    protected $fillable = ['custom_form_submission_id', 'custom_form_field_id', 'value'];

    public function submission()
    {
        return $this->belongsTo(CustomFormSubmission::class, 'custom_form_submission_id');
    }

    public function field()
    {
        return $this->belongsTo(CustomFormField::class, 'custom_form_field_id');
    }
}
