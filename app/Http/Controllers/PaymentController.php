<?php

namespace App\Http\Controllers;

use App\Http\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request){
        try {
            $data = $this->paymentService->get($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function insert(Request $request){
        try {
            $data = $this->paymentService->insert($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function getByLoanId(Request $request,$loanid){
        try {
            $request->merge(['loan_id',$loanid]);
            $data = $this->paymentService->getByLoanId($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request){
        try {
            $data = $this->paymentService->update($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }
}