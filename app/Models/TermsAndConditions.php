<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAndConditions extends Model
{
    use HasFactory;
    public $table = 'terms_and_conditions';
    
    protected $fillable = [

        'user_id',
        'terms_and_conditions',
        'created_at',
        'updated_at'
        
    ];
}
