<?php
namespace App\Http\Controllers;

use App\Http\Services\ReportService;
use Exception;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request) {
        try {
            $report_type = $request->input('report_type',null);
            $data = [];
            if($report_type=='disbursement') {
                $data = $this->reportService->disbursement($request->all());
            }
            else {
                throw new Exception ('Invalid report type');
            }
            return $this->success($data);
        }
        catch(Exception $e) {
            return $this->error($e->getMessage(),200);
        }
    }
}
?>