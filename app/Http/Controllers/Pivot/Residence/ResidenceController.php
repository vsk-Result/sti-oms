<?php

namespace App\Http\Controllers\Pivot\Residence;

use App\Http\Controllers\Controller;
use App\Services\Pivots\Residence\ResidenceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ResidenceController extends Controller
{
    public function __construct(private ResidenceService $residenceService) {}

    public function index(): View
    {
        $dormitories = $this->residenceService->getDormitories();

        Cache::put('pivots.dormitories', $dormitories);

        return view('pivots.residence.index', compact('dormitories'));
    }
}
