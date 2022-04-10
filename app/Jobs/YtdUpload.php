<?php

namespace App\Jobs;

use Throwable;
use App\Models\DsaUser;
use App\Models\Ytd;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class YtdUpload implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $header;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header)
    {
        $this->data   = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try
        {
            foreach ($this->data as $sale) {
                $saleData = array_combine($this->header, $sale);
                Ytd::create($saleData);
            }
            Ytd::whereNull('dsa_id')->update([
                'dsa_id'=> DB::raw('SUBSTRING_INDEX(dsa_name,"-",1)')
            ]);
            //DB::beginTransaction();
            //Ytd::insert($this->data);
            //DB::commit();
        }
        catch(Throwable $e) {
            $this->failed($e);
        }
        catch(Exception $e) {
            $this->failed($e);
        }
    }

    public function failed(Throwable $exception)
    {
        //DB::rollBack();
        return $exception;
        // Send user notification of failure, etc...
    }
}