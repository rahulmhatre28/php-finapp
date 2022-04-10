<?php

namespace App\Http\Controllers;

use App\Jobs\DailyUpload;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Request;
use Throwable;

class DailyController extends Controller
{

    public function upload(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        ini_set("soap.wsdl_cache_enabled", 0);
        try
        {
            //if (Request()->has('file')) {

                //$file = fopen(Request()->file->getRealPath(),'r');
                $file = file('./111.csv');
                $query = <<<eof
                    LOAD DATA INFILE './111.csv'
                    REPLACE INTO TABLE `daily_data`
                    FIELDS TERMINATED BY ',' optionally ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'
                    IGNORE 1 LINES
                    (`month`,product,profile,dsa_name,flag,state,hub,company_category,seg_gov_flag,market,disbursed_date,units,gross_in_cr,net_in_cr,interest_in_cr,pff_in_cr)
                eof;


                $sql = DB::select($query);

                return response()->json(['status'=>1,'result'=>$sql,'error'=>null]);

                // $records = [];
                // $i=0;
                // $date = Carbon::now()->toDateTimeString();
                // while($csvLine = fgetcsv($file)) {
                //     if($i++==0) continue;
                //     $records[] = [
                //                     'month'=>$csvLine[0],
                //                     'product'=>$csvLine[1],
                //                     'profile'=>$csvLine[2],
                //                     'dsa_id'=>strstr($csvLine[3],'-',true),
                //                     'dsa_name'=>$csvLine[3],
                //                     'flag'=>$csvLine[4],
                //                     'state'=>$csvLine[5],
                //                     'hub'=>$csvLine[6],
                //                     'company_category'=>$csvLine[7],
                //                     'seg_gov_flag'=>$csvLine[8],
                //                     'market'=>$csvLine[9],
                //                     'disbursed_date'=>date_format(date_create($csvLine[10]), 'Y-m-d'),
                //                     'units'=>$csvLine[11],
                //                     'gross_in_cr'=>$csvLine[12],
                //                     'net_in_cr'=>$csvLine[13],
                //                     'interest_in_cr'=>$csvLine[14],
                //                     'pff_in_cr'=>$csvLine[15],
                //                     'created_at'=>$date
                //                 ];
                // }
                // if(count($records)>0) {
                //     $chunks = array_chunk($records, 1000);

                //     $header = ['month','product','profile','dsa_id','dsa_name','flag','state','hub','company_category','seg_gov_flag','market','disbursed_date','units','gross_in_cr','net_in_cr','interest_in_cr','pff_in_cr'];
                //     $batch  = Bus::batch([])->dispatch();

                //     foreach ($chunks as $key => $chunk) {
                //         $batch->add(new DailyUpload($chunk, $header));
                //     }
                //     return response()->json(['status'=>1,'result'=>$batch,'error'=>null]);
                // }
                // throw new Exception('Uploaded Empty CSV file or all records in the file was already exist in DB');
            // }
            // else {
            //     throw new Exception('No file selected. Please upload file.');
            // }
        }
        catch(Throwable $e) {
            return response()->json(['status'=>0,'result'=>null,'error'=>$e->getMessage()]);
        }
        catch(Exception $e) {
            return response()->json(['status'=>0,'result'=>null,'error'=>$e->getMessage()]);
        }
    }
}