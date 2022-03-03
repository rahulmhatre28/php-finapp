<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class LoanDocument extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    protected $table = 'loan_documents';
}
