<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Channel extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name',
    'email',
    'phone',
    'pan',
    'pincode',
    'gst'];


    public function banks() {
        return $this->hasMany(ChannelBank::class);
    }
}
