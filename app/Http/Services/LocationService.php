<?php

namespace App\Http\Services;

use App\Models\Channel;
use App\Http\Repositories\LocationRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Http\Request;

class LocationService
{
    protected $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function getAll($data) {
        return $this->locationRepository->getAll($data);
    }

    public function getByParams($data) {
        return $this->locationRepository->getByParams($data);
    }
}