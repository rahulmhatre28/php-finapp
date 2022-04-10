<?php

namespace App\Http\Services;

use App\Models\User;
use App\Http\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Http\Request;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function saveUserData(Request $request){
        // $validatedData = $data->validate([
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'string|max:255',
        //     'phone' => 'required|string|max:12',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:4',
        //     'role_id' => 'string',
        //     'parent_id' => 'string',
        //     'bank_id' => 'string',
        //     'state_id' => 'string',
        //     'branch' => 'string',
        // ]);
        $data = $request->all();
        return $this->userRepository->save($data);
    }

    public function getAll($data) {
        return $this->userRepository->getAll($data);
    }

    public function getById($data) {
        return $this->userRepository->getById($data);
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
        return $this->userRepository->update($data,$data['id']);
    }

    public function getChildDdl($data) {
        return $this->userRepository->getChildDdl($data);
    }

    public function dashboard($data) {
        return $this->userRepository->dashboard($data);
    }
    
    public function ddl($data) {
        return $this->userRepository->ddl($data);
    }
}