<?php

namespace App\Http\Controllers;

use App\Http\Services\LoanService;
use App\Http\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $loanService;
    protected $userService;

    public function __construct(LoanService $loanService, UserService $userService)
    {
        $this->loanService = $loanService;
        $this->userService = $userService;
    }

    public function index(Request $request){
        try {
            $loan = $this->loanService->dashboard($request);
            $user = $this->userService->dashboard($request);
            $record = array_merge($loan,$user);
            return $this->success($record);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }
}