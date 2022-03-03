<?php

namespace App\Http\Repositories;

use App\Models\Loan;
use App\Models\LoanApplicant;
use App\Models\LoanDocument;
use App\Models\User;
use App\Models\LoanLender;
use App\Traits\Mailer;
use Illuminate\Http\Request;
use DB;

class LoanRepository
{
    protected $loan;
    protected $user;
    protected $loanApplicant;
    protected $loanLender;
    use Mailer;

    public function __construct(Loan $loan,User $user,LoanApplicant $loanApplicant,LoanDocument $loanDocument, LoanLender $loanLender)
    {
        $this->loan = $loan;
        $this->user = $user;
        $this->loanApplicant = $loanApplicant;
        $this->loanDocument = $loanDocument;
        $this->loanLender = $loanLender;
    }

    public function save(Request $data)
    {
        try {
            if($data['roleid']==9) {
                $data['executive']=$data['parentid'];
            }
            DB::beginTransaction();
            $loan = new $this->loan;
            $loan->ref_no = time();
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
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$fullpath,
                        'size'=>0,
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
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$fullpath,
                        'size'=>0,
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
                    $fullpath = '/documents/'.time(). $file->getClientOriginalName();
                    $file->move(public_path() . '/documents/', $name);
                    $documents[] = [
                        'loan_id'=>$loan->id,
                        'file_name'=>$fullpath,
                        'size'=>0,
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
            ->select(DB::raw('loan_view.id,loan_view.ref_no,loan_view.applicant_name,loan_view.sales_person_lbl,loan_view.created_at,JSON_ARRAYAGG(JSON_OBJECT("bank_id",loan_lenders.bank_id,"bank_name",banks.bank_name,"bank_user_id",loan_lenders.bank_user_id,"username","Unknown")) as lenders,loan_view.loan_status'))
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
        return $this->loan::with('banks')->find($data['id']);
    }

    public function update($data,$id) {
        $loan = $this->loan::find($id);
        $loan->name = $data['name'];
        $loan->email = $data['email'];
        $loan->phone = $data['phone'];
        $loan->pan = $data['pan'];
        $loan->pincode = $data['pincode'];
        $loan->gst = $data['gst'];

        $this->channelBank::where(['channel_id'=>$id])->delete();

        $channel_banks = new $this->channelBank;
        $banks=[];
        foreach($data['banks'] as $a){
            array_push($banks,[
               'channel_id'=> $loan->id,
               'bank'=> $a['bank'],
               'branchname'=> $a['branchname'],
               'accountno'=> $a['accountno'],
               'accounttype'=> $a['accounttype'],
               'ifsccode'=> $a['ifsccode'],
               'created_at'=> date('Y-m-d H:i:s'),
            ]);
        }
        $channel_banks->insert($banks);
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
                    $lender = json_decode($lender);
                    $lenders[]=[
                        'loan_id'=>$data['loan_id'],
                        'bank_id'=>$lender->bank_id,
                        'bank_user_id'=>$lender->bank_user_id,
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
}