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

        $filterObjectsStatuses = [
            'active' => 'Активные',
            'closed' => 'Закрытые',
            'all' => 'Все',
        ];
        $filterObjectsStatus = $request->get('objects_status', 'active');

        if ($filterObjectsStatus === 'active') {
            $objects = BObject::active()->orderBy('code')->get();
        } elseif ($filterObjectsStatus === 'closed') {
            $objects = BObject::closed()->orderBy('code')->get();
        } else {
            $objects = BObject::activeAndClosed()->orderBy('code')->get();
        }

//        $objects = BObject::active()->whereNotIn('code', ['353', '346', '362', '368', '359'])->orderBy('code')->get();
        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = $objects->whereIn('id', auth()->user()->objects->pluck('id'))->sortBy('code')->get();
        }

        $filteredObjects = $objects;
//
//        if ($request->has('object_id')) {
//            $filteredObjects = BObject::whereIn('id', $request->get('object_id'))->orderBy('code')->get();
//        }

        $activeObjectIds = $filteredObjects->pluck('id')->toArray();
        $activeObjects = BObject::whereIn('id', $activeObjectIds)->orderByDesc('code')->get();
        $acts = $this->actService->filterActs(['object_id' => $activeObjectIds, 'currency' => 'RUB'], $total, false);
        $actsEUR = $this->actService->filterActs(['object_id' => $activeObjectIds, 'currency' => 'EUR'], $total, false);

        return view(
            'pivots.acts-category.index',
            compact(
                'acts', 'activeObjectIds', 'objects', 'activeObjects', 'actsEUR', 'filterObjectsStatuses'
            )
        );
    }
}
