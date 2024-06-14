<?php

namespace App\Http\Controllers\UploadDebtStatus;

use App\Http\Controllers\Controller;
use App\Services\UploadDebtStatusService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UploadDebtStatusController extends Controller
{
    private UploadDebtStatusService $uploadDebtStatusService;

    public function __construct(UploadDebtStatusService $uploadDebtStatusService)
    {
        $this->uploadDebtStatusService = $uploadDebtStatusService;
    }

    public function index(): View
    {
        $items = $this->uploadDebtStatusService->getDebtsStatus();
        return view('upload-debts-status.index', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
       $this->uploadDebtStatusService->uploadDebts($request->toArray());
        return redirect()->back();
    }
}
