<?php

namespace App\Http\Controllers\Object\Report\ActCategory;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use Illuminate\Contracts\View\View;

class ActCategoryController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function index(BObject $object): View
    {
        $total = [];
        $acts = $this->actService->filterActs(['object_id' => [$object->id]], $total);
        return view('objects.tabs.reports.act_category', compact('acts', 'object'));
    }
}
