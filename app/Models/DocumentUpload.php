<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentUpload extends Model
{
    use HasFactory;
    protected $table = 'documents';
    protected $fillable = [
        'user_id',
        'pan_card_front',
        'pan_card_back',
        'adhar_card_front',
        'adhar_card_back'
    ];
}
