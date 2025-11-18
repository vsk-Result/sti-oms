<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        // Организации для разбивки проживания
        $splitResidenceOrganizationsNames = [
            'ИП Кадинова Елена Николаевна',
            'Общество с ограниченной ответственностью "АРТИСТ ПЛЮС"',
            'АО "ИНТЕРМЕТСЕРВИС"',
            'АРТИСТ ПЛЮС ООО',
            'ВЕКТОР ООО',
            'ЕЛИВТ ООО'
        ];
        $splitResidenceOrganizations = Organization::whereIn('name', $splitResidenceOrganizationsNames)->get();

        return view('reports.index', compact('splitResidenceOrganizations'));
    }
}
