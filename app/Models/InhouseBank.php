<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class InhouseBank extends Model
{

    protected $table = "inhouse_banks";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bank',
        'branchname',
        'accountno',
        'accounttype',
        'ifsccode'
    ];

    public function channel() {
        return $this->belongsTo(Channel::class);
    }
}
