<?php

namespace App\Http\Services;

use App\Http\Repositories\ReportRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportService
{
    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function disbursement($data) {
        return $this->reportRepository->disbursement($data);
    }
}