<?php

namespace App\Services\Schema;

use App\Helpers\Sanitizer;
use App\Models\CurrencyExchangeRate;
use App\Models\Schema\Interaction;
use App\Models\Status;
use Illuminate\Database\Eloquent\Collection;

class InteractionService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function updateInteraction(array $requestData): void
    {
        foreach ($requestData['id'] as $index => $id) {
            $interaction = Interaction::find($id);

            $interaction->update([
                'currency' => $requestData['currency'][$index],
                'amount' => $this->sanitizer->set($requestData['amount'][$index])->toAmount()->get(),
            ]);
        }
    }

    public function getInteractions(): array
    {
        $names = Interaction::getNames();
        foreach (Interaction::whereNotIn('name', $names)->get() as $name) {
            $this->createInteraction(['name' => $name, 'currency' => 'RUB', 'amount' => 0]);
        }

        $result = [];
        $interactions = Interaction::whereIn('name', $names)->get();

        foreach ($interactions as $interaction) {
            $result[] = [
                'name' => $interaction->name,
                'currency' => $interaction->currency,
                'amount' => $interaction->amount == 0
                    ? '-'
                    : CurrencyExchangeRate::format($interaction->amount, $interaction->currency),
            ];
        }

        return $result;
    }

    public function getInteractionsByCompany(string $company): Collection
    {
        return Interaction::where('name', 'LIKE', $company . '%')->get();
    }

    private function createInteraction(array $requestData): void
    {
        Interaction::create([
            'name' => $requestData['name'],
            'currency' => $requestData['currency'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'status_id' => Status::STATUS_ACTIVE
        ]);
    }
}
