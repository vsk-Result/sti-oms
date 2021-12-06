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
        $avansImports = AvansImport::orderByDesc('id')->take(30)->with('items', 'items.avans')->get();
        foreach ($avansImports as $import) {
            $sum = 0;

            foreach ($import->items as $item) {
                $sum += $item->avans->value;
            }

            \Log::info($import->id . ' ' . $sum . ' | ' . $paymentAmount);
            \Log::info($sum === $paymentAmount ? 'true' : 'false');
            \Log::info($sum == $paymentAmount ? 'true' : 'false');
            \Log::info((float) $sum == $paymentAmount ? 'true' : 'false');
            \Log::info(gettype($paymentAmount));
            \Log::info(gettype($sum));
            \Log::info($paymentAmount / $sum);

            if ($sum === $paymentAmount) {
                $imports[$import->id] = Carbon::parse($import->date)->format('d.m.Y') . ' | ' . number_format($sum, 2, '.', ' ') . ' | ' . $import->description;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Данные успешно получены',
            'imports' => $imports
        ]);
    }
}
