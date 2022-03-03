<?php

namespace App\Http\Repositories;

use App\Models\Inhouse;
use App\Models\InhouseBank;

class InhouseRepository
{
    protected $inhouse;

    public function __construct(Inhouse $inhouse, InhouseBank $inhouseBank)
    {
        $this->inhouse = $inhouse;
        $this->inhouseBank = $inhouseBank;
    }

    public function save($data)
    {
        $inhouse = new $this->inhouse;
        $inhouse->name = $data['name'];
        $inhouse->email = $data['email'];
        $inhouse->phone = $data['phone'];
        $inhouse->pan = $data['pan'];
        $inhouse->pincode = $data['pincode'];
        $inhouse->gst = $data['gst'];
        $inhouse->created_at = date('Y-m-d H:i:s');
        $inhouse->created_by = $data['userid'];
        $inhouse->save();

        $inhouse_banks = new $this->inhouseBank;
        $inhouse_banks->inhouse_id = $inhouse->id;
        $inhouse_banks->bank = $data['bank'];
        $inhouse_banks->branchname = $data['branchname'];
        $inhouse_banks->accountno = $data['accountno'];
        $inhouse_banks->accounttype = $data['accounttype'];
        $inhouse_banks->ifsccode = $data['ifsccode'];
        $inhouse_banks->created_at = date('Y-m-d H:i:s');
        $inhouse_banks->save();

        return $inhouse->fresh();
    }

    public function getAll($data) {
        $event = json_decode($data['lazyEvent'],true);
        $filters = [];
        foreach(['name','email','phone','pan','gst','pincode','created_at'] as $a) {
            if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                $filters[$a] = $event['filters'][$a]['value'];
            }
        }
        $obj = new $this->inhouse;
        $result = $obj->where($filters)->whereIn('created_by',$data['childs'])->offset($event['first'])->limit($event['rows'])->get();
        $total = $obj->where($filters)->count();
        return ['records'=>$result,'total'=>$total];
    }

    public function getById($data) {
        return $this->inhouse::with('banks')->find($data['id']);
    }

    public function update($data,$id) {
        $inhouse = $this->inhouse::find($id);
        $inhouse->name = $data['name'];
        $inhouse->email = $data['email'];
        $inhouse->phone = $data['phone'];
        $inhouse->pan = $data['pan'];
        $inhouse->pincode = $data['pincode'];
        $inhouse->gst = $data['gst'];

        $this->inhouseBank::where(['inhouse_id'=>$id])->delete();

        $inhouse_banks = new $this->inhouseBank;
        $banks=[];
        foreach($data['banks'] as $a){
            array_push($banks,[
               'inhouse_id'=> $inhouse->id,
               'bank'=> $a['bank'],
               'branchname'=> $a['branchname'],
               'accountno'=> $a['accountno'],
               'accounttype'=> $a['accounttype'],
               'ifsccode'=> $a['ifsccode'],
               'created_at'=> date('Y-m-d H:i:s'),
            ]);
        }
        $inhouse_banks->insert($banks);
        return $inhouse->fresh();
    }

    public function delete($id) {
        $record =  $this->inhouse->find($id);
        if(!empty($record)){
            $this->inhouseBank->where('inhouse_id',$id)->delete();
            $record->delete();
        } else {
            return false;
        }
        return true;
    }
}