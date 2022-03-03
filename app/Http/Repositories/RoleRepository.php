<?php

namespace App\Http\Repositories;

use App\Models\Role;

class RoleRepository
{
    /**
     * @var Role
     */
    protected $Role;

    /**
     * RoleRepository constructor.
     *
     * @param Role $Role
     */
    public function __construct(Role $Role)
    {
        $this->Role = $Role;
    }

    /**
     * Get all Roles.
     *
     * @return Role $Role
     */
    public function getAll()
    {
        return $this->Role
            ->get();
    }

    /**
     * Get Role by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->Role
            ->where('id', $id)
            ->get();
    }

    /**
     * Save Role
     *
     * @param $data
     * @return Role
     */
    public function save($data)
    {
        $Role = new $this->Role;

        $Role->name = $data->name;
        $Role->menus = json_encode($data->menus);
        if(!$data->active){
            $Role->deleted_at = date('Y-m-d H:i:s');
        } else {
            $Role->deleted_at = null;
        }
        $Role->created_at = date('Y-m-d H:i:s');
        $Role->created_by = 1;
        $Role->updated_by = 0;
        $Role->save();
        return $Role->fresh();
    }

    /**
     * Update Role
     *
     * @param $data
     * @return Role
     */
    public function update($data, $id)
    {
        
        $Role = $this->Role->find($id);

        $Role->name = $data->name;
        $Role->menus = json_encode($data->menus);
        if(!$data->active){
            $Role->deleted_at = date('Y-m-d H:i:s');
        } else {
            $Role->deleted_at = null;
        }
        $Role->updated_at = date('Y-m-d H:i:s');
        $Role->updated_by = 1;
        $Role->update();

        return $Role;
    }

    /**
     * Update Role
     *
     * @param $data
     * @return Role
     */
    public function delete($id)
    {
        
        $Role = $this->Role->find($id);
        $Role->delete();

        return $Role;
    }

}