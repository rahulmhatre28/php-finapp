<?php

namespace App\Jobs;

use Throwable;
use App\Models\DsaUser;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DsaUserUpload implements ShouldQueue
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
        $userdata=[];
        foreach ($this->data as $sale) {
            $saleData = array_combine($this->header, $sale);
            DsaUser::create($saleData);
            $owner = explode(" ",$sale['owner_name']);
            $userdata[]=[
                'email'=>$sale['dsa_id'],
                'first_name'=>$owner[0],
                'last_name'=>$owner[1],
                'password'=>$sale['password'],
                'role_id'=>3,
                'parent_id'=>2,
            ];
            User::insert($saleData);
        }
    }

    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
}