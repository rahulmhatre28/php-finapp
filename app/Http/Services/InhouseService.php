<?php

namespace App\Http\Services;

use App\Models\Inhouse;
use App\Http\Repositories\InhouseRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class InhouseService
{
    protected $inhouseRepository;

    public function __construct(InhouseRepository $inhouseRepository)
    {
        $this->inhouseRepository = $inhouseRepository;
    }


    public function saveUserData($data){
        // $validatedData = $data->validate([
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'phone' => 'required|string|max:12',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:4',
        //     'role_id' => 'string',
        //     'parent_id' => 'string',
        // ]);
        return $this->inhouseRepository->save($data);
    }

    public function getAll($data) {
        return $this->inhouseRepository->getAll($data);
    }

    public function getById($data) {
        return $this->inhouseRepository->getById($data);
    }

    public function updateUser($data) {
        // $validatedData = $data->validate([
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'phone' => 'required|string|max:12',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     //'password' => 'required|string|min:8',
        //     //'role_id' => 'string',
        //     'parent_id' => '',
        // ]);
        return $this->inhouseRepository->update($data,$data['id']);
    }

    public function deleteUser($id) {
        return $this->inhouseRepository->delete($id);
    }
}