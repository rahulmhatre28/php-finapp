<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DsaUser extends Model {
    
    protected $table= 'dsa_users'; 
    protected $fillable = ['dsa_id','dsa_name','mobile','owner_name','password','created_at'];
}

?>