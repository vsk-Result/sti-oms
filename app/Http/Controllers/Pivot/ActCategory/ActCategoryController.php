<?php

namespace App\Http\Controllers\Pivot\ActCategory;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ActCategoryController extends Controller
{
    public function index(): View
    {
        return view(
            'pivots.acts-category.index',
            compact(
                []
            )
        );
    }
}
