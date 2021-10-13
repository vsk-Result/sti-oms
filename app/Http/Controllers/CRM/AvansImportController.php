<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\AvansImport;
use Carbon\Carbon;

class AvansImportController extends Controller
{
    public function index()
    {
        $imports = [];
        $avansImports = AvansImport::orderByDesc('id')->take(5)->with('items', 'items.avans')->get();
        foreach ($avansImports as $import) {
            $sum = 0;

            foreach ($import->items as $item) {
                $sum += $item->avans->value;
            }

            $imports[$import->id] = Carbon::parse($import->date)->format('d.m.Y') . ' | ' . number_format($sum, 2, '.', ' ') . ' | ' . $import->description;
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Данные успешно получены',
            'imports' => $imports
        ]);
    }
}
