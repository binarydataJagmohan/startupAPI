<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $table = 'business_details';
    protected $fillable = [
        'user_id',
        'business_name',
        'reg_businessname',
        'website_url',
        'stage',
        'department',
        'startup_date',
        'description',
        'primary_residence',
        'prev_experience',
        'experience',
        'cofounder',
        'logo',
        'none_select',
        'kyc_purposes',
        'tagline',
        'sector',
        'business_file'
    ];

    protected $appends = ['logo'];

    public function getLogoAttribute()
    {
        return url('docs/'.$this->attributes['logo']);
    }
}
