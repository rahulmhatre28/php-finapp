<?php

namespace App\Http\Services;

use App\Models\Channel;
use App\Http\Repositories\ChannelRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class ChannelService
{
    protected $channelRepository;

    public function __construct(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
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
        return $this->channelRepository->save($data);
    }

    public function getAll($data) {
        return $this->channelRepository->getAll($data);
    }

    public function getById($data) {
        return $this->channelRepository->getById($data);
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
        return $this->channelRepository->update($data,$data['id']);
    }

    public function deleteUser($id) {
        return $this->channelRepository->delete($id);
    }

    public function dropdown($data) {
        return $this->channelRepository->dropdown($data);
    }

    public function borrowerdropdown($data) {
        return $this->channelRepository->borrowerdropdown($data);
    }

    public function banks($data) {
        return $this->channelRepository->banks($data);
    }
}