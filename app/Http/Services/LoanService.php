<?php

namespace App\Http\Services;

use App\Models\Channel;
use App\Http\Repositories\LoanRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Http\Request;

class LoanService
{
    protected $loanRepository;

    public function __construct(LoanRepository $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }


    public function saveUserData(Request $data){
        return $this->loanRepository->save($data);
    }

    public function getAll($data) {
        return $this->loanRepository->getAll($data);
    }

    public function getById($data) {
        return $this->loanRepository->getById($data);
    }

    public function updateUser($data) {
        return $this->loanRepository->update($data,$data['id']);
    }

    public function deleteUser($id) {
        return $this->loanRepository->delete($id);
    }

    public function assignLender($request) {
        return $this->loanRepository->assignLender($request);
    }
}