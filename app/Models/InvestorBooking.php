<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorBooking extends Model
{
    use HasFactory;

       protected $fillable = [
        'user_id',
        'business_id',
        'business_unit_id',
        'repayment_date',
        'repayment_value',
        'no_of_units',
    ];
}
