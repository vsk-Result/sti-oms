<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SchedulerController extends Controller
{
    public function index(): View
    {
        return view('scheduler.index');
    }
}
