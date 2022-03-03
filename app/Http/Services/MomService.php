<?php

namespace App\Http\Services;

use App\Models\Mom;
use App\Http\Repositories\MomRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Http\Request;

class MomService
{
    protected $momRepository;

    public function __construct(MomRepository $momRepository)
    {
        $this->momRepository = $momRepository;
    }


    public function saveUserData(Request $data){
        return $this->momRepository->save($data);
    }

    public function getAll($data) {
        return $this->momRepository->getAll($data);
    }

    public function getById($data) {
        return $this->momRepository->getById($data);
    }

    public function updateUser($data) {
        return $this->momRepository->update($data,$data['id']);
    }

    public function deleteUser($id) {
        return $this->momRepository->delete($id);
    }

    public function getByParams($data) {
        return $this->momRepository->getByParams($data);
    }
}