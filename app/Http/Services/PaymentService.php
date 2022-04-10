<?php

namespace App\Http\Services;

use App\Models\Channel;
use App\Http\Repositories\PaymentRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Http\Request;

class PaymentService
{
    protected $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function get($data) {
        return $this->paymentRepository->get($data);
    }

    public function insert($data) {
        return $this->paymentRepository->insert($data);
    }

    public function getByLoanId($data) {
        return $this->paymentRepository->getByLoanId($data);
    }

    public function update($data) {
        return $this->paymentRepository->update($data);
    }
}