<?php


namespace App\Http\Controllers;

use App\Imports\Import;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    use ApiResponse;
    
    public function import(Request $request)
    {   
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new Import();
            Excel::import($import, $request->file('file'));

            return $this->responseSuccess('Import completed successfully');

        } catch (\Exception $e) {
           
            $decoded = json_decode($e->getMessage(), true);
            
            if ($decoded && isset($decoded['errors'])) {
                return response()->json([ 
                    'message' => $decoded['message'],
                    'errors' => $decoded['errors']
                ], 422);
            }

            return $this->responseServerError('An error occurred during import: ' . $e->getMessage());
        }
    }
    
}