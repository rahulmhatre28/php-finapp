<?php

namespace App\Http\Repositories;

use App\Http\Services\RoleService;
use App\Models\Menu;
use Exception;

class MenuRepository
{
    /**
     * @var Menu
     */
    protected $Menu;
    protected $roleService;

    /**
     * MenuRepository constructor.
     *
     * @param Menu $Menu
     */
    public function __construct(Menu $Menu,RoleService $roleService)
    {
        $this->Menu = $Menu;
        $this->roleService = $roleService;
    }

    /**
     * Get all Menus.
     *
     * @return Menu $Menu
     */
    public function getAll()
    {
        return $this->Menu
            ->get();
    }

    /**
     * Get Menu by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->Menu
            ->where('id', $id)
            ->get();
    }

    /**
     * Save Menu
     *
     * @param $data
     * @return Menu
     */
    public function save($data)
    {
        $Menu = new $this->Menu;

        $Menu->title = $data['title'];
        $Menu->description = $data['description'];

        $Menu->save();

        return $Menu->fresh();
    }

    /**
     * Update Menu
     *
     * @param $data
     * @return Menu
     */
    public function update($data, $id)
    {
        
        $Menu = $this->Menu->find($id);

        $Menu->title = $data['title'];
        $Menu->description = $data['description'];

        $Menu->update();

        return $Menu;
    }

    /**
     * Update Menu
     *
     * @param $data
     * @return Menu
     */
    public function delete($id)
    {
        
        $Menu = $this->Menu->find($id);
        $Menu->delete();

        return $Menu;
    }

    public function menuAccess($data)
    {
        $menu = $this->Menu::where('code',$data['code'])->first();
        if(!empty($menu)){
            $role = (Object) $this->roleService->getById($data['roleid']);
            if(!empty($role)) {
                $roleMenus = json_decode($role->menus);
                // Find menu exist in logged in user role
                $key = array_filter($roleMenus,function($element) use($menu){
                    return $element->id===$menu->id;
                });
                $key = array_values($key);
                if(count($key)>0) {
                    return $key;
                }
                else {
                    throw new Exception('Menu access Denied');
                }
            }
            else {
                throw new Exception('Role doesnt exist');
            }
        }
        else {
            throw new Exception('Menu doesnt exist');
        }
    }

    public function menuListByRole($data) {
        $role = (Object) $this->roleService->getById($data['roleid']);
        if(!empty($role)) {
            $roleMenus = json_decode($role->menus);
            $roleFilterMenu = [];
            foreach($roleMenus as $r) {
                if(!empty($r->action)) {
                    array_push($roleFilterMenu,$r->id);
                }
            }
            $menu = $this->Menu::where('active',1)->whereIn('id',$roleFilterMenu);
            $parentId = [];
            
            foreach($menu->get() as $m) {
                array_push($parentId,$m->parent_id);
            }

            $parentMenu = $this->Menu::whereIn('id',$parentId)->union($menu)->orderBy('order')->get();
            return $parentMenu;
        }
        else {
            return [];
        } 
    }

}