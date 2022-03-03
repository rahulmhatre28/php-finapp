<?php

namespace App\Http\Controllers;

use App\Http\Services\LocationService;
use Exception;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index(Request $request){
        try {
            $data = $this->locationService->getAll($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }

    public function getByParams(Request $request){
        try {
            $data = $this->locationService->getByParams($request);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),500);
        }
    }
}