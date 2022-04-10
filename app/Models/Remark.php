<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Remark extends Model
{

   protected $table = 'remarks';

   public function loan(){
      return $this->belongsToMany(Loan::class);
   }

   public function user() {
      return $this->hasOne(User::class,'id','created_by');
   }

}
