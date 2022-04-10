<?php

namespace App\Http\Controllers;

use App\Jobs\DsaUserUpload;
use App\Models\DsaUser;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Request;
use Throwable;

class DsaUserController extends Controller
{

    public function upload(Request $request)
    {
        try
        {
            
            if(Request()->file->getClientOriginalExtension()!='csv'){
                throw new Exception('Please upload .csv extension file');
            }
            else if (Request()->has('file')) {

                $file = fopen(Request()->file->getRealPath(),'r');
                $users = DsaUser::pluck('dsa_id')->toArray();

                $records = [];
                while($csvLine = fgetcsv($file)) {
                    if(!in_array($csvLine[0],$users)) {
                        $records[] = [
                                        'dsa_id'=>$csvLine[0],
                                        'dsa_name'=>$csvLine[1],
                                        'mobile'=>$csvLine[2],
                                        'owner_name'=>$csvLine[3],
                                        'password'=>bcrypt($csvLine[2]),
                                        'created_at'=>Carbon::now()->toDateTimeString()
                                    ];
                    }
                }
                
                array_shift($records);
                if(count($records)>0) {
                    $chunks = array_chunk($records, 1000);

                    $header = ['dsa_id','dsa_name','mobile','owner_name','password','created_at'];
                    $batch  = Bus::batch([])->dispatch();

                    foreach ($chunks as $key => $chunk) {
                        $batch->add(new DsaUserUpload($chunk, $header));
                    }
                    return $this->success($batch,'File uploaded successfully');
                }
                throw new Exception('Uploaded Empty CSV file or all records in the file was already exist in DB');
            }
            else {
                throw new Exception('No file selected. Please select file.');
            }
        }
        catch(Throwable $e) {
            return $this->error($e->getMessage(),200);
        }
        catch(Exception $e) {
            return $this->error($e->getMessage(),200);
        }
    }

    public function batch()
    {
        $batchId = request('id');
        return Bus::findBatch($batchId);
    }

    public function batchInProgress()
    {
        $batches = DB::table('job_batches')->where('pending_jobs', '>', 0)->get();
        if (count($batches) > 0) {
            return Bus::findBatch($batches[0]->id);
        }

        return [];
    }
}