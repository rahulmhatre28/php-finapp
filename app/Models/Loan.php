<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Location;

class Loan extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        
    ];

    protected $table = 'loans';

    public function lenders(){
        return $this->hasMany(LoanLender::class);
    }

    public function applicants(){
       return $this->hasMany(LoanApplicant::class);
    }

    public function documents(){
        return $this->hasMany(LoanDocument::class);
    }

    public function executiveList() {
        return $this->hasOne(User::class,'id','sales_person_id');
    }

    public function parent() {
        return $this->hasOne(User::class,'id','parent_id');
    }

    public function remarks() {
        return $this->hasMany(Remark::class);
    }

    public function executive() {
        return $this->hasOne(User::class,'id','sales_person_id');
    }

    public function channel() {
        return $this->hasOne(Channel::class,'id','channel_id');
    }

    public function product() {
        return $this->hasOne(Mom::class,'key','loan_type')->whereIn('group',['business_loan','salaried_loan','other_loans']);
    }
}
