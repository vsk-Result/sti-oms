<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Illuminate\Contracts\View\View;

class ActController extends Controller
{
    public function index(BObject $object): View
    {
        $acts = $object->acts()->with('payments')->get();
        return view('objects.tabs.acts', compact('object', 'acts'));
    }
}
