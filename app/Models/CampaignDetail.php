<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignDetail extends Model
{
    use HasFactory;
    protected $table = 'campaign_details';

    protected $fillable = [

        'ccsp_fund_id',



    ];

}
