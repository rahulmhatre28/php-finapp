<?php

namespace App\Http\Controllers;

use App\Http\Services\LoanService;
use Exception;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function insert(Request $request){
        try {
            $data = $this->loanService->saveUserData($request);
            return $this->success($data,'Details saved successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function index(Request $request){
        try {
            $data = $this->loanService->getAll($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function getById(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->loanService->getById($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->loanService->updateUser($request);
            return $this->success($data,'Details updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }

    public function delete($id){
        try {
            $data = $this->loanService->deleteUser($id);
            return $this->success($data,'Record deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }

    public function assignLender(Request $request) {
        try {
            $data = $this->loanService->assignLender($request);
            return $this->success($data,'Lender assigned successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function assignPerson(Request $request) {
        try {
            $data = $this->loanService->assignPerson($request);
            return $this->success($data,'Sales Person assigned successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function disbursed(Request $request) {
        try {
            $data = $this->loanService->disbursed($request);
            return $this->success($data,'Loan disbursed successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function download(Request $request,int $id) {
        try {
            $data = $this->loanService->download($id);
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length: ".filesize($data));
            header("Content-Disposition: attachment; filename=\"".basename($data)."\"");
            readfile($data);
        exit;
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }
}