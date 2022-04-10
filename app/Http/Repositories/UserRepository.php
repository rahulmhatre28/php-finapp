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
        if($data['role_id']==7) {
            $user->parent_id = $data['userid'];
            $user->bank_id = $data['bank_id'];
            $user->state_id = $data['state_id'];
            $user->branch = $data['branch'];
        } else {
            $user->parent_id = empty($data['parent_id'])?0:$data['parent_id'];
        }
        $user->password = Hash::make($data['password']);
        if(!$data['active']){
            $user->deleted_at = date('Y-m-d H:i:s');
        } else {
            $user->deleted_at = null;
        }
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
        if($data['roleid']==7) {
            $user->parent_id = $data['userid'];
        } else {
            $user->parent_id = $data['parent_id'];
        }
        $user->bank_id = $data['bank_id'];
        $user->state_id = $data['state_id'];
        if($data['password']!=='@@@@@@@@') {
            $user->password = Hash::make($data['password']);
        }
        if(!$data['active']){
            //$user->deleted_at = date('Y-m-d H:i:s');
        } else {
            $user->deleted_at = null;
        }
        $user->branch = $data['branch'];
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
            $children = $this->user::select(['id','first_name','last_name'])
                        ->where('parent_id',$data['id'])
                        ->where('role_id',($data['type']+1))
                        ->get();
        }
        return $children;
    }

    public function dashboard($data) {
        $users = $this->user::
                whereIn('parent_id',$data['childs'])
                ->where('role_id',9)
                ->whereBetween('created_at',[$data['fromdate'],$data['todate']])->count();

        return ['users'=>$users];

    }

    public function ddl($data) {
        $users = $this->user::with(['role' => function ($query) {
            $query->select('id','name');
        }])
        ->select('id','first_name','last_name','role_id')
        ->whereIn('parent_id',$data['childs'])
        ->get();

        return $users;

    }
}