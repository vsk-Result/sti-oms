<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\AvansImport;
use Illuminate\View\View;

class SplitAvansImportController extends Controller
{
    public function index(): View
    {
        $imports = AvansImport::with('items', 'company')
            ->orderBy('date', 'desc')
            ->paginate(25);

        return view('crm-split-avans-imports.index', compact('imports'));
    }
}
