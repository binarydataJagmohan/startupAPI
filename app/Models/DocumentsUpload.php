<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentsUpload extends Model
{
    use HasFactory;
    protected $table = 'document_upload';
    protected $fillable = [
        'user_id',
        'filename',
        'filepath',
        'type'
    ];
}