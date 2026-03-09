<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TemplateExport;
use Essa\APIToolKit\Api\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;

class ExportTemplateController extends Controller
{
    use ApiResponse;
    public function export()
    {
        return Excel::download(new TemplateExport, 'Template.xlsx');
    }
}
