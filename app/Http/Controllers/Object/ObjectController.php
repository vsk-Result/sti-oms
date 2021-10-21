<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class ObjectController extends Controller
{
    public function index(): View
    {
        $objects = BObject::withCount(['payments' => function($query) {
            $query->where('date', '>', Carbon::now()->subWeeks(2)->format('Y-m-d'));
        }])->orderByDesc('payments_count')->orderByDesc('code')->get();

        return view('objects.index', compact('objects'));
    }
}
