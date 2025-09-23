<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class WarningOrganizationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $file = $request->file('file');

        // Закинем в папку для ручных загрузок
        Storage::putFileAs('public/objects-debts-manuals', $file, 'warning_organizations.xls');

        // Запустим крон задачу на импорт долгов по подрядчикам
        Artisan::call('oms:import-warning-organizations-data-from-1c-excel');

        return redirect()->back();
    }
}
