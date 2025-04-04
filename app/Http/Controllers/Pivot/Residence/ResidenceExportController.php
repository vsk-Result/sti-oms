<?php

namespace App\Http\Controllers\Pivot\Residence;

use App\Exports\Pivot\Residence\Export;
use App\Http\Controllers\Controller;
use App\Services\Pivots\Residence\ResidenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResidenceExportController extends Controller
{
    public function __construct(private ResidenceService $residenceService) {}

    public function store(Request $request): BinaryFileResponse
    {
        $formattedDate = $request->get('date');
        $dormitories = Cache::get('pivots.dormitories', []);
        $date = get_date_and_month_from_string($formattedDate);
        $dormitoryId = $request->get('dormitory_id', '');

        $dormitoryName = '';
        foreach ($dormitories as $dormitory) {
            if ($dormitory['Id'] === $dormitoryId) {
                $dormitoryName = $dormitory['Name'];
                break;
            }
        }

        $residenceInfo = $this->residenceService->getResidenceInfo($date, $dormitoryId);

        return Excel::download(
            new Export($residenceInfo, $formattedDate, $dormitoryName),
            'Анализ проживания за ' . $formattedDate . '.xlsx'
        );
    }
}
