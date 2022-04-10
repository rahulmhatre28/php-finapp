<?php

namespace App\Http\Repositories;

use App\Models\Loan;
use App\Models\LoanApplicant;
use App\Models\LoanDocument;
use App\Models\User;
use App\Models\LoanLender;
use App\Models\Channel;
use App\Models\Remark;
use App\Models\Mom;
use App\Traits\Mailer;
use Illuminate\Http\Request;
use DB;
use Exception;
use ZipArchive;

class LoanRepository
{
    protected $loan;
    protected $user;
    protected $loanApplicant;
    protected $loanLender;
    protected $channel;
    protected $remark;
    protected $mom;
    use Mailer;

    public function __construct(Loan $loan,User $user,LoanApplicant $loanApplicant,LoanDocument $loanDocument, LoanLender $loanLender, Channel $channel, Remark $remark, Mom $mom)
    {
        $this->loan = $loan;
        $this->user = $user;
        $this->loanApplicant = $loanApplicant;
        $this->loanDocument = $loanDocument;
        $this->loanLender = $loanLender;
        $this->channel = $channel;
        $this->remark = $remark;
        $this->mom = $mom;
    }

    public function save(Request $data)
    {
        try {
            if($data['roleid']==9) {
                $data['executive']=$data['parentid'];
            }
            $ref_no = $this->generateRefNo($data);
            DB::beginTransaction();
            $loan = new $this->loan;
            $loan->ref_no = $ref_no;
            $loan->loan_through = $data->loan_through;
            $loan->borrower_id = ($data->borrower_id=='null')?null:$data->borrower_id;
            $loan->channel_id = ($data->channel_id=='null')?null:$data->channel_id;
            $loan->loan_option_type = $data->loan_option_type;
            $loan->loan_type = $data->loan_type;
            $loan->loan_other_type = $data->loan_other_type;
            $loan->annual_pat = (($data->annual_pat=='null')?0:$data->annual_pat);
            $loan->annual_pat_inlakhs = (int) $data->annual_pat_inlakhs;
            $loan->loan_amount = (($data->loan_amount=='null')?0:$data->loan_amount);
            $loan->loan_amount_inlakhs = (int) $data->loan_amount_inlakhs;
            $loan->loan_product_group = $data->loan_product_group;
            $loan->property_type = $data->property_type;
            $loan->annual_turnover = (($data->annual_turnover=='null')?0:$data->annual_turnover);
            $loan->annual_turnover_inlakhs = (int) $data->annual_turnover_inlakhs;
            $loan->loan_usage_type = $data->loan_usage_type;
            $loan->loan_sub_type = $data->loan_sub_type;
            $loan->business_type = $data->business_type;
            $loan->business_name = $data->business_name;
            $loan->business_years = $data->business_years;
            $loan->business_years_inyear = (int) $data->business_years_inyear;
            $loan->business_gst = $data->business_gst;
            $loan->business_pincode = (($data->business_pincode=='null')?0:$data->business_pincode);
            $loan->business_location = (($data->business_location=='null')?0:$data->business_location);
            $loan->business_city = (($data->business_city=='null')?0:$data->business_city);
            $loan->business_state = (($data->business_state=='null')?0:$data->business_state);
            $loan->business_address_line_1 = $data->business_address_line_1;
            $loan->business_address_line_2 = $data->business_address_line_2;
            $loan->existing_profile = $data->existing_profile;
            $loan->borrower_type = $data->borrower_type;
            $loan->net_income = (($data->net_income=='null')?0:$data->net_income);
            $loan->net_income_inlakhs = (int) $data->net_income_inlakhs;
            $loan->gross_income = (($data->gross_income=='null')?0:$data->gross_income);
            $loan->gross_income_inlakhs = (int) $data->gross_income_inlakhs;
            $loan->created_at = date('Y-m-d H:i:s');
            $loan->created_by = $data->userid;
            $loan->updated_by = $data->userid;
            $loan->sales_person_id = $data->executive;
            $loan->save();

            $loanApplicant = new $this->loanApplicant;
            $applicants=[];
            foreach($data->applicants as $a) {
                $a = json_decode($a);
                $applicants[]=[
                    'loan_id'=>$loan->id,
                    'fname'=>$a->fname,
                    'lname'=>$a->lname,
                    'email'=>$a->email,
                    'phone_1'=>$a->phone_1,
                    'phone_2'=>$a->phone_2,
                    'pincode'=>$a->pincode,
                    'locality'=>$a->locality,
                    'city'=>$a->city,
                    'state'=>$a->state,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'created_by' => $data->userid,
                    'updated_by' => $data->userid
                ];
            }
            $loanApplicant->insert($applicants);

            $documents=[];
            if ($data->hasfile('kyc_files')) {
                foreach ($data->file('kyc_files') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'kyc',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if ($data->hasfile('finance_files')) {
                foreach ($data->file('finance_files') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'finance',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if ($data->hasfile('other_files')) {
                foreach ($data->file('other_files') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'other',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if(count($documents)>0) {
                $loanDocument = new $this->loanDocument;
                $loanDocument->insert($documents);   
            }
            DB::commit();
            return $loan->fresh();
        }
        catch (\PDOException $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function getAll($data) {
        $event = json_decode($data['lazyEvent'],true);
        $pagetype = $data['pagetype'];
        if($pagetype!=='application')
        {
            $result = DB::table('loan_view')
            ->select(DB::raw('loan_view.id,loan_view.ref_no,loan_view.applicant_name,loan_view.sales_person_lbl,loan_view.created_at,JSON_ARRAYAGG(JSON_OBJECT("bank_id",loan_lenders.bank_id,"bank_name",banks.bank_name,"bank_user_id",loan_lenders.bank_user_id,"username",concat(users.first_name,\' \',users.last_name))) as lenders,loan_view.loan_status,loan_view.channel_id,concat(u.first_name,\' \',u.last_name) as disbursed_lbl,loan_view.disbursed_at'))
            ->join('loan_lenders','loan_lenders.loan_id','=','loan_view.id')
            ->join('users','users.id','=','loan_lenders.bank_user_id')
            ->join('banks','banks.id','=','loan_lenders.bank_id')
            ->leftJoin('users as u','u.id','=','loan_view.disbursed_by')
            ->where(function($query) use ($event) {
                foreach(['ref_no','applicant_name','sales_person_lbl','created_at'] as $a) {
                    if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                        $query->where($a,'LIKE',"%" . $event['filters'][$a]['value'] . "%");
                    }
                }
            })
            ->where(function($query) use($pagetype){
                if($pagetype=='assigned') {
                    $query->whereNotNull('loan_status');
                    $query->where('loan_disbursed',false);
                }
                else if($pagetype=='disbursed') {
                    $query->where('loan_disbursed',true);
                } else {
                    $query->whereNull('loan_status');
                }
            })
            ->whereIn('created_by',$data['childs'])
            ->groupBy(['loan_view.id','loan_view.ref_no','loan_view.applicant_name','loan_view.sales_person_lbl','loan_view.created_at','loan_view.loan_status'])
            ->offset($event['first'])
            ->limit($event['rows']
            )->get();
            $total = DB::table('loan_view')
                    ->join('loan_lenders','loan_lenders.loan_id','=','loan_view.id')
                    ->join('banks','banks.id','=','loan_lenders.bank_id')
                    ->where(function($query) use ($event) {
                        foreach(['ref_no','applicant_name','sales_person_lbl','created_at'] as $a) {
                            if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                                $query->where($a,'LIKE',"%" . $event['filters'][$a]['value'] . "%");
                            }
                        }
                    })
                    ->where(function($query) use($pagetype){
                        if($pagetype=='assigned') {
                            $query->whereNotNull('loan_status');
                            $query->where('loan_disbursed',false);
                        }
                        else if($pagetype=='disbursed') {
                            $query->where('loan_disbursed',true);
                        } else {
                            $query->whereNull('loan_status');
                        }
                    })
                    ->count();
        }
        else {
            $result = DB::table('loan_view')
            ->select(DB::raw('loan_view.id,loan_view.ref_no,loan_view.applicant_name,loan_view.sales_person_lbl,loan_view.created_at,loan_view.loan_status'))
            ->where(function($query) use ($event) {
                foreach(['ref_no','applicant_name','sales_person_lbl','created_at'] as $a) {
                    if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                        $query->where($a,'LIKE',"%" . $event['filters'][$a]['value'] . "%");
                    }
                }
            })
            ->where(function($query) use($pagetype){
                if($pagetype=='assigned') {
                    $query->whereNotNull('loan_status');
                    $query->where('loan_disbursed',false);
                }
                else if($pagetype=='disbursed') {
                    $query->where('loan_disbursed',true);
                } else {
                    $query->whereNull('loan_status');
                }
            })
            ->whereIn('created_by',$data['childs'])
            ->groupBy(['loan_view.id','loan_view.ref_no','loan_view.applicant_name','loan_view.sales_person_lbl','loan_view.created_at','loan_view.loan_status'])
            ->offset($event['first'])
            ->limit($event['rows']
            )->get();
            $total = DB::table('loan_view')
                    ->where(function($query) use ($event) {
                        foreach(['ref_no','applicant_name','sales_person_lbl','created_at'] as $a) {
                            if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                                $query->where($a,'LIKE',"%" . $event['filters'][$a]['value'] . "%");
                            }
                        }
                    })
                    ->where(function($query) use($pagetype){
                        if($pagetype=='assigned') {
                            $query->whereNotNull('loan_status');
                            $query->where('loan_disbursed',false);
                        }
                        else if($pagetype=='disbursed') {
                            $query->where('loan_disbursed',true);
                        } else {
                            $query->whereNull('loan_status');
                        }
                    })
                    ->count();
        }
        return ['records'=>$result,'total'=>$total];
    }

    public function getById($data) {
        return $this->loan::where('id',$data['id'])->with(['lenders','applicants','documents','executiveList'=>function($query){
            $query->select('id','first_name','last_name','parent_id');
        },'executiveList.parent.parent.parent'=>function($query){
            $query->select('id','first_name','last_name','parent_id');
        }])->first();
    }

    public function update($data,$id) {
        try {
            DB::beginTransaction();
            $loan = $this->loan::find($id);
            $loan->channel_id = $data->channel_id;
            $loan->loan_option_type = $data->loan_option_type;
            $loan->loan_type = $data->loan_type;
            $loan->loan_other_type = $data->loan_other_type;
            $loan->annual_pat = (($data->annual_pat=='null')?0:$data->annual_pat);
            $loan->annual_pat_inlakhs = (int) $data->annual_pat_inlakhs;
            $loan->loan_amount = (($data->loan_amount=='null')?0:$data->loan_amount);
            $loan->loan_amount_inlakhs = (int) $data->loan_amount_inlakhs;
            $loan->loan_product_group = $data->loan_product_group;
            $loan->property_type = $data->property_type;
            $loan->annual_turnover = (($data->annual_turnover=='null')?0:$data->annual_turnover);
            $loan->annual_turnover_inlakhs = (int) $data->annual_turnover_inlakhs;
            $loan->loan_usage_type = $data->loan_usage_type;
            $loan->loan_sub_type = $data->loan_sub_type;
            $loan->business_type = $data->business_type;
            $loan->business_name = $data->business_name;
            $loan->business_years = $data->business_years;
            $loan->business_years_inyear = (int) $data->business_years_inyear;
            $loan->business_gst = $data->business_gst;
            $loan->business_pincode = (($data->business_pincode=='null')?0:$data->business_pincode);
            $loan->business_location = (($data->business_location=='null')?0:$data->business_location);
            $loan->business_city = (($data->business_city=='null')?0:$data->business_city);
            $loan->business_state = (($data->business_state=='null')?0:$data->business_state);
            $loan->business_address_line_1 = $data->business_address_line_1;
            $loan->business_address_line_2 = $data->business_address_line_2;
            $loan->existing_profile = $data->existing_profile;
            $loan->borrower_type = $data->borrower_type;
            $loan->net_income = (($data->net_income=='null')?0:$data->net_income);
            $loan->net_income_inlakhs = (int) $data->net_income_inlakhs;
            $loan->gross_income = (($data->gross_income=='null')?0:$data->gross_income);
            $loan->gross_income_inlakhs = (int) $data->gross_income_inlakhs;
            $loan->created_at = date('Y-m-d H:i:s');
            $loan->created_by = $data->userid;
            $loan->updated_by = $data->userid;
            $loan->sales_person_id = $data->executive;
            $loan->update();
            $this->loanApplicant::where(['loan_id'=>$id])->delete();
            $loanApplicant = new $this->loanApplicant;
            $applicants=[];
            foreach($data->applicants as $a) {
                $a = json_decode($a);
                $applicants[]=[
                    'loan_id'=>$loan->id,
                    'fname'=>$a->fname,
                    'lname'=>$a->lname,
                    'email'=>$a->email,
                    'phone_1'=>$a->phone_1,
                    'phone_2'=>$a->phone_2,
                    'pincode'=>$a->pincode,
                    'locality'=>$a->locality,
                    'city'=>$a->city,
                    'state'=>$a->state,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'created_by' => $data->userid,
                    'updated_by' => $data->userid
                ];
            }
            $loanApplicant->insert($applicants);
            
            $documents=[];
            if ($data->hasfile('kyc_files')) {
                foreach ($data->file('kyc_files') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'kyc',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if ($data->hasfile('finance_files')) {
                foreach ($data->file('finance_files') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'finance',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if ($data->hasfile('other_files')) {
                foreach ($data->file('other_files') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'other',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if(count($documents)>0) {
                $loanDocument = new $this->loanDocument;
                $loanDocument->insert($documents);   
            }
            DB::commit();
            return $loan->fresh();
        }
        catch (\PDOException $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return $loan->fresh();
    }

    public function delete($id) {
        $record =  $this->loan->find($id);
        if(!empty($record)){
            $this->channelBank->where('channel_id',$id)->delete();
            $record->delete();
        } else {
            return false;
        }
        return true;
    }

    public function assignLender($data) {
        $record =  $this->loan->find($data['loan_id']);
        if(!empty($record)) {
            DB::beginTransaction();
            $record->loan_status=$data['loan_status'];
            $record->update();
            if(count($data['lenders'])>0) {
                $lenderobj = $this->loanLender->where(['loan_id'=>$data['loan_id']]);
                $lenderobj->delete();
                $lenders=[];
                foreach($data['lenders'] as $lender) {
                    $lenders[]=[
                        'loan_id'=>$data['loan_id'],
                        'bank_id'=>$lender['bank_id'],
                        'bank_user_id'=>$lender['bank_user_id'],
                    ];
                }
                $this->loanLender->insert($lenders);
            }
            DB::commit();
        }
        else {
            throw new \Exception('Loan doesnt exist.');
        }
    }

    public function assignPerson($data) {
        $record =  $this->loan->find($data['loan_id']);
        if(!empty($record)) {
            DB::beginTransaction();
            if($record->channel_id!=$data['channel_id']) {
                $channelObj = $this->channel->find($data['channel_id']);
                $channelObj->executive = $data['executive'];
            }
            $record->sales_person_id=$data['executive'];
            $record->update();
            DB::commit();
        }
        else {
            throw new \Exception('Loan doesnt exist.');
        }
    }

    public function disbursed($data) {
        try {
            DB::beginTransaction();
            $loan = $this->loan::find($data['loan_id']);
            if($data->sanctioned_amount>$loan->loan_amount) {
                throw new Exception('Sanctioned amount should not be greater than loan amount');
            }
            $loan->loan_disbursed = 1;
            $loan->disbursed_by = $data->userid;
            $loan->loan_status = $data->loan_status;
            $loan->disbursed_at = $data->disbursed_date;
            $loan->channel_payout_percent = $data->channel_payout_percent;
            $loan->lender_payout_percent = $data->lender_payout_percent;
            $loan->sanctioned_amount = $data->sanctioned_amount;
            $loan->processing_fee = $data->processing_fee;
            $loan->lender_loan_id = $data->lender_loan_id;
            $loan->disbursed_at = $data->disbursed_date;
            $loan->updated_by = $data->userid;
            $loan->update();
            
            $documents=[];
            if ($data->hasfile('loan_repay_doc')) {
                foreach ($data->file('loan_repay_doc') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'loan_repay',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if ($data->hasfile('channel_invoice_doc')) {
                foreach ($data->file('channel_invoice_doc') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'channel_invoice',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if ($data->hasfile('llc_doc')) {
                foreach ($data->file('llc_doc') as $file) {
                    $name = time(). $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$file->getClientOriginalName(),
                        'url'=>$fullpath,
                        'size'=>$size,
                        'doc_type'=>'llc',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by' => $data->userid,
                        'updated_by' => $data->userid
                    ];
                }
            }

            if(count($documents)>0) {
                $loanDocument = new $this->loanDocument;
                $loanDocument->insert($documents);   
            }

            if($data['remark']!='null') {
                $remark = new $this->remark;
                $remark->loan_id=$loan->id;
                $remark->remark=$data['remark'];
                $remark->created_at=date('Y-m-d H:i:s');
                $remark->created_by=$data->userid;
                $remark->save();
            }
            DB::commit();
            return $loan->fresh();
        }
        catch (\PDOException $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function download($id) {
        $docs = $this->loanDocument::where('loan_id',$id)->get();
        $zip = new ZipArchive();
        $zip->open('documents.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach($docs as $doc) {
            $file = public_path().$doc; 
            if (file_exists($file))
            {
                $zip->addFile($file, '/');
            }
        }
        header('Location:'.public_path().'documents.zip');
        die();
    }

    public function dashboard($data) {
        $id = ($data['uid']=='null')?$data['childs']:[$data['uid']];
        $application = $this->loan::where('loan_disbursed',0)
                ->whereIn('created_by',$id)
                ->where('loan_status','')
                ->whereBetween('created_at',[$data['fromdate'],$data['todate']])->count();

        $sanctioned = $this->loan::whereIn('created_by',$id)
                ->where('loan_status','<>','')
                ->where('loan_disbursed','=',0)
                ->whereBetween('created_at',[$data['fromdate'],$data['todate']])->count();
        
        $disbursed = $this->loan::where('loan_disbursed',1)
                ->whereIn('created_by',$id)
                ->whereBetween('disbursed_at',[$data['fromdate'],$data['todate']])->count();

        $graph = $this->loan::selectRaw('date_format((case when (loan_disbursed=0) then created_at else disbursed_at end),"%m") as month,count(1) as count,case when (loan_status="" and loan_disbursed=0) then "application" when (loan_status<>"" and loan_disbursed=0) then "assigned" when (loan_disbursed=1) then "disbursed" else null end as type')
                 //->groupBy(['loan_disbursed','created_at','disbursed_at','loan_status'])
                 ->groupBy(DB::Raw('date_format((case when (loan_disbursed=0) then created_at else disbursed_at end),"%m"),case when (loan_status="" and loan_disbursed=0) then "application" when (loan_status<>"" and loan_disbursed=0) then "assigned" when (loan_disbursed=1) then "disbursed" else null end'))
                 ->get()
                 ->toArray();
        $final = [['label'=>'Application','backgroundColor'=>'#EC407A'],['label'=>'Assigned','backgroundColor'=>'#AB47BC'],['label'=>'Disbursed','backgroundColor'=>'#42A5F5']];

        $months = ['01','02','03','04','05','06','07','08','09','10','11','12'];
        
        foreach($final as $key=>&$f)
        {
            $f['yAxisID']='y';
            foreach($months as $m) {
                foreach($graph as $g) {
                    if($m==$g['month'] && strtolower($f['label'])==strtolower($g['type'])) {
                        $f['data'][]=$g['count'];
                        break;
                    }
                }
                $f['data'][]=0;
            }
        }
        
        return ['application'=>$application,'sanctioned'=>$sanctioned,'disbursed'=>$disbursed,'graph'=>$final];

    }

    private function generateRefNo($data) {
        $loanType = $this->mom::select('code')
        ->whereIn('group',['business_loan','salaried_loan','other_loans'])
        ->where('key',$data['loan_type'])->first();
        if(!empty($loanType)) {
            $loanCode=$loanType->code;
            return $loanCode.date('Ymd').'1';
        }
        else {
            throw new Exception('Loan code is missing. Couldnt generate reference number.');
        }
    }
    
    public function getDisbursedDetails($data) {
        return $this->loan::where('id',$data['id'])->with(['lenders','applicants','documents','remarks','remarks.user'=>function($query){
            $query->select('id','first_name','last_name');
        }])->first();
    }
}