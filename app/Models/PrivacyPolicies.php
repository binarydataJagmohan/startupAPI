<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicies extends Model
{
    use HasFactory;
    public $table = 'privacy_policies';
    
    protected $fillable = [

        'user_id',
        'privacy_policies',
        'created_at',
        'updated_at'
        
    ];
}
