<?php

namespace App\Http\Controllers;

use App\Http\Services\MomService;
use Exception;
use Illuminate\Http\Request;

class MomController extends Controller
{
    protected $momService;

    public function __construct(MomService $momService)
    {
        $this->momService = $momService;
    }

    public function insert(Request $request){
        try {
            $data = $this->momService->saveUserData($request);
            return $this->success($data,'Details saved successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function index(Request $request){
        try {
            $data = $this->momService->getAll($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function getById(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->momService->getById($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->momService->updateUser($request);
            return $this->success($data,'Details updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }

    public function delete($id){
        try {
            $data = $this->momService->deleteUser($id);
            return $this->success($data,'Record deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }

    public function getByParams(Request $request){
        try {
            $data = $this->momService->getByParams($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }
}