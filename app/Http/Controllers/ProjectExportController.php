<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProjectExport;
use Essa\APIToolKit\Api\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;

class ProjectExportController extends Controller
{
    use ApiResponse;
    
    public function export(Request $request)
    {
        try {
            $fileName = 'projects_' . date('Y-m-d_His') . '.xlsx';
            
            
            return Excel::download(
                new ProjectExport($request->all()), 
                $fileName
            );
            
        } catch (\Exception $e) {
            return $this->responseServerError('Export failed: ' . $e->getMessage());
        }
    }
}