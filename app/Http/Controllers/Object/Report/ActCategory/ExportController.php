<?php
namespace App\Http\Controllers\Object\Report\ActCategory;

use App\Exports\Object\Report\ActCategory\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function store(BObject $object): BinaryFileResponse
    {
        return Excel::download(new Export($this->actService, $object), 'Отчет по категориям.xlsx');
    }
}
