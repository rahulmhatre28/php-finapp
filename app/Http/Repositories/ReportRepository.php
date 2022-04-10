<?php

namespace App\Http\Repositories;

use App\Models\Loan;

class ReportRepository
{
    protected $loan;
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function disbursement($data) {
       return Loan::with(['executive'=>function($query){
                    $query->select(['id','first_name','last_name','email']);
                },'channel','channel.banks'])
                ->where('loan_disbursed',1)
                ->get(); 
    }
}