<?php

namespace App\Http\Repositories;

use App\Http\Services\UserService;
use App\Models\Channel;
use App\Models\ChannelBank;
use App\Models\User;
use App\Traits\Mailer;
use Illuminate\Http\Request;
use DB;

class ChannelRepository
{
    protected $channel;
    protected $channelBank;
    protected $user;
    protected $userService;
    use Mailer;

    public function __construct(Channel $channel, ChannelBank $channelBank,User $user,UserService $userService)
    {
        $this->channel = $channel;
        $this->channelBank = $channelBank;
        $this->user = $user;
        $this->userService = $userService;
    }

    public function save($data)
    {
        // create login .. insert data in user table
        $userRequest = new Request([
            'first_name'=>$data['name'],
            'last_name'=>'',
            'email'=>$data['email'],
            'phone'=>$data['phone'],
            'role_id'=>'9',
            'parent_id'=>$data['executive'],
            'password'=>(string) $data['phone']
        ]);
        $createdUser = $this->userService->saveUserData($userRequest);

        if(!empty($createdUser)) {
            $channel = new $this->channel;
            $channel->name = $data['name'];
            $channel->email = $data['email'];
            $channel->phone = $data['phone'];
            $channel->pan = $data['pan'];
            $channel->pincode = $data['pincode'];
            $channel->gst = $data['gst'];
            $channel->created_at = date('Y-m-d H:i:s');
            $channel->created_by = $data['userid'];
            $channel->user_id = $createdUser->id;
            $channel->executive = $createdUser->parent_id;
            $channel->save();
        }

        // $channel_banks = new $this->channelBank;
        // $channel_banks->channel_id = $channel->id;
        // $channel_banks->bank = $data['bank'];
        // $channel_banks->branchname = $data['branchname'];
        // $channel_banks->accountno = $data['accountno'];
        // $channel_banks->accounttype = $data['accounttype'];
        // $channel_banks->ifsccode = $data['ifsccode'];
        // $channel_banks->created_at = date('Y-m-d H:i:s');
        // $channel_banks->save();

        $par = $this->user->select(['email'])->whereIn('id',$data['parents'])->get()->toArray();

        foreach($par as $a) {
            $this->sendMail($a['email'],'New user created','Hi new user created');
        }

        return $channel->fresh();
    }

    public function getAll($data) {
        $event = json_decode($data['lazyEvent'],true);
        $filters = [];
        foreach(['name','email','phone','pan','gst','pincode','created_at'] as $a) {
            if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                $filters[$a] = $event['filters'][$a]['value'];
            }
        }
        $obj = new $this->channel;
        $result = $obj->where($filters)->whereIn('created_by',$data['childs'])->orWhere('executive',$data['userid'])->offset($event['first'])->limit($event['rows'])->get();
        $total = $obj->where($filters)->count();
        return ['records'=>$result,'total'=>$total];
    }

    public function getById($data) {
        if($data['roleid']==9) {
            return $this->channel::with('banks')->where('user_id',$data['id'])->first();
        }
        return $this->channel::with(['banks','executiveList'=>function($query){
            $query->select('id','first_name','last_name','parent_id');
        },'executiveList.parent.parent.parent'=>function($query){
            $query->select('id','first_name','last_name','parent_id');
        }])->find($data['id']);
    }

    public function update($data,$id) {
        $channel = $this->channel::find($id);
        $channel->name = $data['name'];
        $channel->email = $data['email'];
        $channel->phone = $data['phone'];
        $channel->pan = $data['pan'];
        $channel->pincode = $data['pincode'];
        $channel->gst = $data['gst'];

        $this->channelBank::where(['channel_id'=>$id])->delete();

        $channel_banks = new $this->channelBank;
        $banks=[];
        foreach($data['banks'] as $a){
            array_push($banks,[
               'channel_id'=> $channel->id,
               'bank'=> $a['bank'],
               'branchname'=> $a['branchname'],
               'accountno'=> $a['accountno'],
               'accounttype'=> $a['accounttype'],
               'ifsccode'=> $a['ifsccode'],
               'created_at'=> date('Y-m-d H:i:s'),
            ]);
        }
        $channel_banks->insert($banks);
        return $channel->fresh();
    }

    public function delete($id) {
        $record =  $this->channel->find($id);
        if(!empty($record)){
            $this->channelBank->where('channel_id',$id)->delete();
            $record->delete();
        } else {
            return false;
        }
        return true;
    }

    public function dropdown($data) {
        $obj = new $this->channel;
        $result = $obj->select('id','name')->where('executive',$data['id'])->orderBy('name')->get();
        return $result;
    }

    public function borrowerdropdown($data) {
        $obj = new $this->user;
        $result = $obj->select('id','first_name','last_name')->where('parent_id',$data['id'])->where('role_id',8)->orderBy('first_name')->get();
        return $result;
    }

    public function banks($data) {
        $obj = new $this->channelBank;
        $result = $obj->select('id','bank','branchname')->where('channel_id',$data['id'])->orderBy('bank')->get();
        return $result;
    }
}