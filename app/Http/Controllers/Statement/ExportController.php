<?php

namespace App\Http\Controllers\Statement;

use App\Exports\Statement\StatementExport;
use App\Http\Controllers\Controller;
use App\Models\Statement;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(Statement $statement): BinaryFileResponse
    {
        return Excel::download(
            new StatementExport($statement),
            'Экспорт выписки за ' . $statement->date . ' (' . $statement->company->short_name . ').xlsx'
        );
    }
}
