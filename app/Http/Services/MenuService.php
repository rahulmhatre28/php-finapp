<?php

namespace App\Http\Services;

use App\Models\Role;
use App\Http\Repositories\MenuRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class MenuService
{
    /**
     * @var $menuRepository
     */
    protected $menuRepository;

    /**
     * RoleService constructor.
     *
     * @param menuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
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
            $Role = $this->menuRepository->delete($id);

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
        return $this->menuRepository->getAll();
    }

    /**
     * Get Role by id.
     *
     * @param $id
     * @return String
     */
    public function getById($id)
    {
        return $this->menuRepository->getById($id);
    }

    /**
     * Update Role data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function updateMenu($data, $id)
    {
        $validator = Validator::make($data, [
            'title' => 'bail|min:2',
            'description' => 'bail|max:255'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        DB::beginTransaction();

        try {
            $Role = $this->menuRepository->update($data, $id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());

            throw new InvalidArgumentException('Unable to update Role data');
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
    public function saveMenuData($data)
    {
        $validator = Validator::make($data, [
            'title' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $result = $this->menuRepository->save($data);

        return $result;
    }

    public function menuAccess($data)
    {
        return $this->menuRepository->menuAccess($data);
    }

    public function menuListByRole($data)
    {
        return $this->menuRepository->menuListByRole($data);
    }

}