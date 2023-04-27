<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorTerms extends Model
{
    use HasFactory;
    public $table = 'investor_terms';
    
    protected $fillable = [
        'user_id',
        'principal_residence',
        'cofounder',
        'prev_investment_exp',
        'experience',
        'net_worth',
        'no_requirements',
        'annual_income',
        'financial_net_worth',
        'financial_annual_net_worth',
        'foreign_annual_income',
        'foreign_net_worth',
        'foreign_annual_net_worth',
        'corporate_net_worth',
    ];
}
