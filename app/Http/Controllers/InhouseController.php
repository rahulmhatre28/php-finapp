<?php

namespace App\Http\Controllers;

use App\Http\Services\InhouseService;
use Exception;
use Illuminate\Http\Request;

class InhouseController extends Controller
{
    protected $inhouseService;

    public function __construct(InhouseService $inhouseService)
    {
        $this->inhouseService = $inhouseService;
    }

    public function insert(Request $request){
        try {
            $data = $this->inhouseService->saveUserData($request);
            return $this->success($data,'Details saved successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function index(Request $request){
        try {
            $data = $this->inhouseService->getAll($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function getById(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->inhouseService->getById($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->inhouseService->updateUser($request);
            return $this->success($data,'Details updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }

    public function delete($id){
        try {
            $data = $this->inhouseService->deleteUser($id);
            return $this->success($data,'Record deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }
}