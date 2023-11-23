<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ifinworth extends Model
{
    use HasFactory;

    protected $table = 'ifinworth_details'; 

    protected $fillable = [
        'startup_id',
        'ccsp_fund_id',
        'round_of_ifinworth',
        'ifinworth_currency',
        'ifinworth_amount',
        'pre_committed_ifinworth_currency',
        'pre_committed_ifinworth_amount',
        'pre_committed_investor',
        'accredited_investors',
        'angel_investors',
        'regular_investors',
        'other_funding_detail',
        'pitch_deck',
        'one_pager',
        'previous_financials',
        'latest_cap_table',
        'other_documents',  
        'status',
        'approval_status'
       

    ];    
}
