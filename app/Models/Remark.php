<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Remark extends Model
{

   protected $table = 'remarks';

   public function loan(){
      return $this->belongsTo(Loan::class);
   }

}
