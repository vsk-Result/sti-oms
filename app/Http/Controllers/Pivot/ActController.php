<?php

namespace App\Http\Controllers\Pivot;

use App\Http\Controllers\Controller;
use App\Services\Contract\ActService;
use Illuminate\View\View;

class ActController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function index(): View
    {
        $pivot = $this->actService->getPivot();
        return view('pivots.acts.index', compact('pivot'));
    }
}
