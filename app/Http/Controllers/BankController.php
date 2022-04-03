<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models;
use App\Models\Bank;

class BankController extends Controller
{

    public function __construct()
    {
       
    }

    public function list(Request $request) {
        $bank = Bank::all();
        return $this->success($bank);
    }

    public function lenders(Request $request) {
        $bank = Bank::query()->with(['users'=>function($query){
            return $query->select('id','first_name','last_name','bank_id');
        }])->get();
        return $this->success($bank);
    }

    
}