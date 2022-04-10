<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ytd extends Model {
    
    protected $table= 'ytd_data'; 
    protected $fillable = ['month','product','profile','dsa_id','dsa_name','flag','state','hub','company_category','seg_gov_flag','market','disbursed_date','units','gross_in_cr','net_in_cr','interest_in_cr','pff_in_cr'];
    protected $dates= ['disbursed_date'];

    // public function setDisbursedDateAttribute($value)
    // {
    //  $this->attributes['disbursed_date'] = date_format(date_create($value), 'Y-m-d');
    // }

}

?>