<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function save($data)
    {
        $user = new $this->user;
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->role_id = $data['role_id'];
        $user->parent_id = empty($data['parent_id'])?0:$data['parent_id'];
        $user->password = Hash::make($data['password']);
        // if(!$data['active']){
        //     $user->deleted_at = date('Y-m-d H:i:s');
        // } else {
        //     $user->deleted_at = null;
        // }
        $user->executive = $data['executive'];
        $user->created_at = date('Y-m-d H:i:s');
        $user->save();
        return $user->fresh();
    }

    public function getAll($data) {
        $event = json_decode($data['lazyEvent'],true);
        $filters = [];
        foreach(['name','email','phone','role','created_at'] as $a) {
            if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                $filters[$a] = $event['filters'][$a]['value'];
            }
        }
        $obj = new $this->user;
        $result = $obj::with(['role' => function ($query) {
            $query->select('id','name');
        },'parent' => function ($query) {
            $query->select('id','first_name','last_name');
        }])->whereIn('parent_id',$data['childs'])
        ->where($filters)
        ->offset($event['first'])
        ->limit($event['rows'])
        ->get();
        $total = $obj->where($filters)->count();
        return ['records'=>$result,'total'=>$total];
    }

    public function getById($data) {
        return $this->user::find($data['id']);
    }

    public function update($data,$id) {
        $user = $this->user::find($id);
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->role_id = $data['role_id'];
        $user->parent_id = $data['parent_id'];
        if($data['password']!=='@@@@@@@@') {
            $user->password = Hash::make($data['password']);
        }
        if(!$data['active']){
            //$user->deleted_at = date('Y-m-d H:i:s');
        } else {
            $user->deleted_at = null;
        }
        $user->executive = $data['executive'];
        $user->updated_at = date('Y-m-d H:i:s');
        $user->update();
        return $user->fresh();
    }

    public function getChildDdl($data) {
        if($data['type']==1){
            $children = $this->user::select(['id','first_name','last_name'])->where('role_id',2)->get();
        }
        else 
        {
            $children = $this->user::select(['id','first_name','last_name'])->where('parent_id',$data['id'])->get();
        }
        return $children;
    }
}