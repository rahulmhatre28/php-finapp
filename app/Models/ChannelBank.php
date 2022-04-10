<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class ChannelBank extends Model
{

    protected $table = "channel_banks";
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

    public function bankDetail() {
        return $this->hasOne(Bank::class,'id','bank_id');
    }
}
