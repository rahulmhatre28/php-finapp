<?php

namespace App\Http\Services;

use App\Models\Role;
use App\Http\Repositories\RoleRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class RoleService
{
    /**
     * @var $RoleRepository
     */
    protected $RoleRepository;

    /**
     * RoleService constructor.
     *
     * @param RoleRepository $RoleRepository
     */
    public function __construct(RoleRepository $RoleRepository)
    {
        $this->RoleRepository = $RoleRepository;
    }

    /**
     * Delete Role by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $Role = $this->RoleRepository->delete($id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());

            throw new InvalidArgumentException('Unable to delete Role data');
        }

        DB::commit();

        return $Role;

    }

    /**
     * Get all Role.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->RoleRepository->getAll();
    }

    /**
     * Get Role by id.
     *
     * @param $id
     * @return String
     */
    public function getById($id)
    {
        return $this->RoleRepository->getById($id);
    }

    /**
     * Update Role data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function updateRole($data, $id)
    {
        DB::beginTransaction();
        try {
            $Role = $this->RoleRepository->update($data, $id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());

            throw new InvalidArgumentException($e->getMessage());
        }

        DB::commit();

        return $Role;

    }

    /**
     * Validate Role data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function saveRoleData($data)
    {
        $result = $this->RoleRepository->save($data);
        return $result;
    }

}