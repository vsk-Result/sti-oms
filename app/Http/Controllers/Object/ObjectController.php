<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Illuminate\Contracts\View\View;

class ObjectController extends Controller
{
    public function index(): View
    {
        $objects = BObject::orderByDesc('code')->get();
        return view('objects.index', compact('objects'));
    }
}
