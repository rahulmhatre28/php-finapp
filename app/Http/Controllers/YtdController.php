<?php

namespace App\Http\Controllers;

use App\Jobs\DsaUserUpload;
use App\Jobs\YtdUpload;
use App\Models\DsaUser;
use App\Models\Ytd;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Http\Request;
use Throwable;

class YtdController extends Controller
{

    public function upload(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        ini_set("soap.wsdl_cache_enabled", 0);
        try
        {
            //'LOAD DATA LOCAL INFILE "filename.csv" INTO TABLE table_name FIELDS TERMINATED BY ',' ENCLOSED BY '"' IGNORE 1 ROWS;'
            //if (!Request()->has('file')) {

                //$file = file(Request()->file->getRealPath());
                $file = file('./current.csv');
                $chunks = array_chunk($file,1000);
                $header = ['month','product','profile','dsa_name','flag','state','hub','company_category','seg_gov_flag','market','disbursed_date','units','gross_in_cr','net_in_cr','interest_in_cr','pff_in_cr'];

                $batch  = Bus::batch([])->dispatch();

                foreach ($chunks as $key => $chunk) {
                    $data = array_map('str_getcsv', $chunk);
                    $batch->add(new YtdUpload($data, $header));
                }

                return response()->json(['status'=>1,'result'=>$batch,'error'=>null]);
        }
        catch(Throwable $e) {
            return response()->json(['status'=>0,'result'=>null,'error'=>$e->getMessage()]);
        }
        catch(Exception $e) {
            return response()->json(['status'=>0,'result'=>null,'error'=>$e->getMessage()]);
        }
    }


    public function report(Request $request) {
        try
        {
            $cal_column = $request->input('type','units');
            if(empty($cal_column)) {
                throw new Exception('Report type is required');
            }
            else {
                if($cal_column=='wirr') {
                    $cal_column='(a.interest_in_cr/a.net_in_cr)';
                    $cal_column1='interest_in_cr,net_in_cr';
                }
                else if($cal_column=='process_fee') {
                    $cal_column='(a.pff_in_cr/1.18/a.net_in_cr)';
                    $cal_column1='pff_in_cr,net_in_cr';
                }
                else {
                    $cal_column1=$cal_column;
                    $cal_column="a.".$cal_column;
                }
            } 
            $date=['null','null'];
            if($request->date!='null') {
                $date = explode(",",$request->date);
            }
            $sql = DB::select("select a.month,a.state,a.hub,sum($cal_column) as total from
            (select `month`,product,profile,dsa_id,dsa_name,flag,state,hub,company_category,seg_gov_flag,market,disbursed_date,units,gross_in_cr,net_in_cr,interest_in_cr,pff_in_cr from ytd_data
            UNION ALL
            select `month`,product,profile,dsa_id,dsa_name,flag,state,hub,company_category,seg_gov_flag,market,disbursed_date,units,gross_in_cr,net_in_cr,interest_in_cr,pff_in_cr from daily_data ) a
            where 
            case when '".$request->product."'='' then true else a.product='".$request->product."' end and
            case when '".$request->state."'='' then true else a.state='".$request->state."' end and
            case when '".$request->hub."'='' then true else a.hub='".$request->hub."' end and
            case when '".$request->profile."'='' then true else a.profile='".$request->profile."' end and
            case when '".$request->company_category."'='' then true else a.company_category='".$request->company_category."' end and
            case when '".$request->seg_gov_flag."'='' then true else a.seg_gov_flag='".$request->seg_gov_flag."' end and
            case when '".$request->market."'='' then true else a.market='".$request->market."' end and
            case when '".$request->month."'='' then true else FIND_IN_SET (LEFT(a.`month`,LOCATE('-',a.`month`) - 1),'".$request->month."') end and
            case when '".$date[0]."'='null' then true else a.disbursed_date BETWEEN '".$date[0]."' AND '".$date[1]."' end
            group by a.month,a.state,a.hub order by a.month,a.state,a.hub");

            $monthQuery = DB::select("select DISTINCT a.month from (select `month`,disbursed_date from ytd_data yd 
            UNION select `month`,disbursed_date from daily_data dd) a
            where 
            case when '".$request->month."'='' then true else FIND_IN_SET (LEFT(a.`month`,LOCATE('-',a.`month`) - 1),'".$request->month."') end and
            case when '".$date[0]."'='null' then true else a.disbursed_date BETWEEN '".$date[0]."' AND '".$date[1]."' end
            order by FIELD(SUBSTRING_INDEX(month,'-',1),'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec')");

            $final=[];
            foreach($sql as $a) {
                if(!isset($final[$a->state])) {
                    $final[$a->state]=[
                        'state'=>$a->state,
                        'data'=>[]
                    ];
                }

                foreach($monthQuery as $m) {
                    $total = round((($a->month==$m->month)?$a->total:0),2);
                    if(!isset($final[$a->state]['data'][$a->hub])) {
                        $final[$a->state]['data'][$a->hub]=[
                            'hub'=>$a->hub,
                            $m->month=>$total,
                            'grand_total'=>$total,
                            //'color'=>'#fff'
                        ];
                    }
                    else 
                    {
                        if(!isset($final[$a->state]['data'][$a->hub][$m->month])){
                            $final[$a->state]['data'][$a->hub][$m->month]=$total;
                            $final[$a->state]['data'][$a->hub]['grand_total'] =round(($final[$a->state]['data'][$a->hub]['grand_total']+$total),2);
                        }
                        else {
                            $final[$a->state]['data'][$a->hub][$m->month] +=$total;
                            $final[$a->state]['data'][$a->hub]['grand_total'] =round(($final[$a->state]['data'][$a->hub]['grand_total']+$total),2);
                        }
                    }
                }
            }
            $final = array_values($final);
            $final = array_map(function($a){
                return ['state'=>$a['state'],'data'=>array_values($a['data'])];
            },$final);

            $grandArray=['state'=>null,'data'=>[]];
            foreach($final as &$f) {
                $temp=['hub'=>null];
                foreach($f['data'] as $d) {
                    $key = array_keys($d);
                    foreach($key as $k){
                        if($k!=='hub') {
                            @$temp[$k] =round(($temp[$k]+$d[$k]),2);
                            //@$temp['color']='#dddddd';
                            //@$f[$k] +=$d[$k];
                            @$grandArray['data']['last'][$k] =round(($grandArray['data']['last'][$k]+$d[$k]),2);
                        }
                        else {
                            @$temp[$k]='Total';
                            @$grandArray['data']['last']['hub'] ='Grand Total';
                            //@$temp['color']='#dddddd';
                        }
                    }
                }
                array_push($f['data'],$temp);
            }
            $grandArray['data'] = array_values($grandArray['data']);
            array_push($final,$grandArray);
            array_unshift($monthQuery,['month'=>'hub']);
            array_push($monthQuery,['month'=>'grand_total']);
            return $this->success([$final,$monthQuery],null);
        }
        catch(Exception $e) {
            return $this->error($e->getMessage(),200);
        }
    }

    public function reportddl(Request $request) {
        $sql = DB::select("select distinct product from ytd_data;");
        $sql1 = DB::select("select distinct profile from ytd_data;");
        $sql2 = DB::select("select distinct state from ytd_data;");
        $sql3 = DB::select("select distinct hub from ytd_data;");
        $sql4 = DB::select("select distinct company_category from ytd_data;");
        $sql5 = DB::select("select distinct seg_gov_flag from ytd_data;");
        $sql6 = DB::select("select distinct market from ytd_data;");
        return $this->success([
            'product'=>$sql,
            'profile'=>$sql1,
            'state'=>$sql2,
            'hub'=>$sql3,
            'company_category'=>$sql4,
            'seg_gov_flag'=>$sql,
            'market'=>$sql,
        ],null);
    }
}