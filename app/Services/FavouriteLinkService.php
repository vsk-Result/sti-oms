<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\FavouriteLink;
use App\Models\Status;

class FavouriteLinkService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function findByLink(string|null $link): FavouriteLink|null
    {
        return FavouriteLink::where('link', $this->sanitizer->set($link)->maxLength(300)->get())->first();
    }

    public function createFavouriteLink(array $requestData): FavouriteLink
    {
        $link = FavouriteLink::create([
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->maxLength(100)->get(),
            'link' => $this->sanitizer->set($requestData['link'])->maxLength(300)->get(),
            'order' => FavouriteLink::getNextOrder(),
            'status_id' => Status::STATUS_ACTIVE
        ]);

        return $link;
    }

    public function updateFavouriteLink(FavouriteLink $link, array $requestData): void
    {
        $link->update([
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->maxLength(100)->get(),
            'status_id' => $requestData['status_id']
        ]);
    }

    public function destroyFavouriteLink(FavouriteLink $link): void
    {
        $link->delete();
    }
}
