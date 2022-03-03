<?php

namespace App\Http\Controllers;

use App\Http\Services\ChannelService;
use Exception;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    protected $channelService;

    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    public function insert(Request $request){
        try {
            $data = $this->channelService->saveUserData($request);
            return $this->success($data,'Details saved successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function index(Request $request){
        try {
            $data = $this->channelService->getAll($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function getById(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->channelService->getById($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request,$id){
        try {
            $request->merge(['id'=>$id]);
            $data = $this->channelService->updateUser($request);
            return $this->success($data,'Details updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }

    public function delete($id){
        try {
            $data = $this->channelService->deleteUser($id);
            return $this->success($data,'Record deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }

    public function dropdown(Request $request){
        try {
            $data = $this->channelService->dropdown($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        } 
    }
}