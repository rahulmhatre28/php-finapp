<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function insert(Request $request){
        try {
            $data = $this->userService->saveUserData($request);
            return $this->success($data,'Details saved successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function index(Request $request){
        try {
            $data = $this->userService->getAll($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function getById(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->userService->getById($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->userService->updateUser($request);
            return $this->success($data,'Details updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }

    public function getchild(Request $request) {
        try {
            $data = $this->userService->getChildDdl($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }
}