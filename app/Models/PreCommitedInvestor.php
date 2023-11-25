<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreCommitedInvestor extends Model
{
    use HasFactory;
    protected $table = 'ifinworth_commited_investor'; 

    protected $fillable = [
        'startup_id',
        'investor_id',  
        'status',      

    ];    
}
