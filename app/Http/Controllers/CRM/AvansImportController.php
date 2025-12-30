<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\AvansImport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvansImportController extends Controller
{
    public function index(Request $request)
    {
        $imports = [];
        $paymentAmount = (float) abs($request->payment_amount);
        $avansImports = AvansImport::orderByDesc('id')->with('items', 'items.avans')->get();
        foreach ($avansImports as $import) {
            $sum = 0;

            foreach ($import->items as $item) {
                if ($item->avans) {
                    $sum += $item->avans->value;
                }
            }

            if ((string) round($sum) === (string) round($paymentAmount)) {
                $imports[$import->id] = Carbon::parse($import->date)->format('d.m.Y') . ' | ' . number_format($sum, 2, '.', ' ') . ' | ' . $import->type;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Данные успешно получены',
            'imports' => $imports
        ]);
    }
}
