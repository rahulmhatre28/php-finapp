<?php

namespace App\Http\Repositories;

use App\Models\Mom;
use App\Traits\Mailer;
use Illuminate\Http\Request;
use DB;

class MomRepository
{
    protected $mom;

    public function __construct(Mom $mom)
    {
        $this->mom = $mom;
       
    }

    public function save(Request $data)
    {
        try {
        }
        catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getAll($data) {
        $event = json_decode($data['lazyEvent'],true);
        $filters = [];
        foreach(['name','email','phone','pan','gst','pincode','created_at'] as $a) {
            if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                $filters[$a] = $event['filters'][$a]['value'];
            }
        }
        $obj = new $this->loan;
        $result = $obj->where($filters)->whereIn('created_by',$data['childs'])->offset($event['first'])->limit($event['rows'])->get();
        $total = $obj->where($filters)->count();
        return ['records'=>$result,'total'=>$total];
    }

    public function getById($data) {
        return $this->mom::find($data['id']);
    }

    public function getByParams($data) {
        return $this->mom::select('key','value','group')->whereIn('group',json_decode($data['groups']))->orderBy('value')->get();
    }

    public function update($data,$id) {
        
        return true;
    }

    public function delete($id) {
        return true;
    }
}