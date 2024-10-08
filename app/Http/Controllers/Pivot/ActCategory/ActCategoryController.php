<?php

namespace App\Http\Controllers\Pivot\ActCategory;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActCategoryController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function index(Request $request): View
    {
        $total = [];

        $objects = BObject::active()->whereNotIn('code', ['353', '346', '362', '368'])->orderBy('code')->get();
        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        $filteredObjects = $objects;

        if ($request->has('object_id')) {
            $filteredObjects = BObject::whereIn('id', $request->get('object_id'))->orderBy('code')->get();
        }

        $activeObjectIds = $filteredObjects->pluck('id')->toArray();
        $activeObjects = BObject::whereIn('id', $activeObjectIds)->orderByDesc('code')->get();
        $acts = $this->actService->filterActs(['object_id' => $activeObjectIds], $total, false);

        return view(
            'pivots.acts-category.index',
            compact(
                'acts', 'activeObjectIds', 'objects', 'activeObjects'
            )
        );
    }
}
