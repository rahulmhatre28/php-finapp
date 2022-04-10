<?php

namespace App\Http\Repositories;

use App\Models\Loan;
use App\Models\Mom;
use App\Models\Payment;
use Carbon\Carbon;
use DB;

class PaymentRepository
{

    protected $loan;
    protected $mom;
    protected $payment;
    public function __construct(Loan $loan, Mom $mom, Payment $payment)
    {  
        $this->loan = $loan;
        $this->mom = $mom;
        $this->payment = $payment;
    }

    public function get($data) {
        $event = json_decode($data['lazyEvent'],true);
        $filters = [];
        foreach(['name','email','phone','pan','gst','pincode','created_at'] as $a) {
            if(isset($event['filters'][$a]) && !empty($event['filters'][$a]['value'])){
                $filters[$a] = $event['filters'][$a]['value'];
            }
        }

        $sql = $this->loan::with(['product'=>function($query){
            $query->select(['key','value']);
        }])
        ->where($filters)
        ->where('loan_disbursed',1)
        ->whereIn('created_by',$data['childs']);

        $result = $sql->offset($event['first'])->limit($event['rows'])->get();
        
        $total = $sql->count();
        return ['records'=>$result,'total'=>$total];
    }

    public function insert($data) {
        $loan = $this->loan::find($data['loan_id']);
        $loan->payment_initiated = $data['payment_status'];
        $loan->update();
        // delete payment entry
        $this->payment->where('loan_id',$data['loan_id'])->update(['deleted_at'=>Carbon::now()]);

        $payment = new $this->payment;
        $payment->loan_id = $loan->id;
        $payment->bank_id = $data['bank_id'];
        $payment->payment_status = $data['payment_status'];
        $payment->note = $data['note'];
        $payment->disbursement_amount = $loan->sanctioned_amount;
        $payment->channel_payout_percent = $loan->channel_payout_percent;
        $payment->payable_amount = ($loan->sanctioned_amount*$loan->channel_payout_percent)/100;
        $payment->net_amount = ($loan->sanctioned_amount*$loan->channel_payout_percent)/100;
        $payment->tax = ((($loan->sanctioned_amount*$loan->channel_payout_percent)/100)*20)/100;
        $payment->net_payable = (($loan->sanctioned_amount*$loan->channel_payout_percent)/100) +(((($loan->sanctioned_amount*$loan->channel_payout_percent)/100)*20)/100);
        $payment->payment_to = ($loan->loan_through==1)?$data['channel_id']:$data['borrower_id'];
        $payment->save();
        return true;
    }

    public function getByLoanId($data) {
        return $this->payment::where('loan_id',$data['loan_id'])->where('deleted_at',null)->first();
    }

    public function update($data) {
        $payment = $this->payment::find($data['id']);
        $payment->approved=1;
        $payment->approved_on=Carbon::now();
        $payment->update();

        $this->loan::where('id',$payment->loan_id)->update(['payment_initiated'=>2]);
        return true;
    }
}