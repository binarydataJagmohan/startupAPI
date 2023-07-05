<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    public $table = 'payments';
    protected $fillable = 
    [
        'user_id',
        'repayment',
        'card_number',
        'expiry_date',
        'cvc',
        'zip_code',
        'status'
    ];
}
