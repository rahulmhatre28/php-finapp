<?php

namespace App\Http\Repositories;

use App\Models\Menu;

class MenuRepository
{
    /**
     * @var Menu
     */
    protected $Menu;

    /**
     * MenuRepository constructor.
     *
     * @param Menu $Menu
     */
    public function __construct(Menu $Menu)
    {
        $this->Menu = $Menu;
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

}