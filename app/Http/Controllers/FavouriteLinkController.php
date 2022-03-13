<?php

namespace App\Http\Controllers;

use App\Services\FavouriteLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavouriteLinkController extends Controller
{
    private FavouriteLinkService $favouriteLinkService;

    public function __construct(FavouriteLinkService $favouriteLinkService)
    {
        $this->favouriteLinkService = $favouriteLinkService;
    }

    public function index()
    {

    }

    public function store(Request $request): JsonResponse
    {
        if ($this->favouriteLinkService->findByLink($request->get('link'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Обнаружено дублирование ссылки. Запись не создана.'
            ]);
        }

        $this->favouriteLinkService->createFavouriteLink($request->toArray());
        return response()->json([
            'status' => 'success',
            'message' => 'Ссылка успешно создана!'
        ]);
    }
}
