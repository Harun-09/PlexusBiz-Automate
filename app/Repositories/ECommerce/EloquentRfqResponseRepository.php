<?php

namespace App\Repositories\ECommerce;

use App\DTOs\ECommerce\RfqResponseData;
use App\Domains\ECommerce\Enums\RfqResponseStatus;
use App\Domains\ECommerce\Models\RfqResponse;
use Illuminate\Database\Eloquent\Builder;

class EloquentRfqResponseRepository implements RfqResponseRepositoryInterface
{
    public function query(): Builder
    {
        return RfqResponse::query();
    }

    public function upsertForSupplier(RfqResponseData $data): RfqResponse
    {
        return RfqResponse::updateOrCreate(
            [
                'rfq_id' => $data->rfqId,
                'supplier_id' => $data->supplierId,
            ],
            array_merge($data->toPersistenceArray(), [
                'status' => RfqResponseStatus::Pending->value,
                'buyer_action_at' => null,
            ]),
        );
    }

    public function markStatus(RfqResponse $response, RfqResponseStatus $status): RfqResponse
    {
        $response->forceFill([
            'status' => $status->value,
            'buyer_action_at' => now(),
        ])->save();

        return $response->refresh();
    }

    public function rejectOtherPendingForRfq(RfqResponse $acceptedResponse): void
    {
        RfqResponse::query()
            ->where('rfq_id', $acceptedResponse->rfq_id)
            ->whereKeyNot($acceptedResponse->id)
            ->where('status', RfqResponseStatus::Pending->value)
            ->update([
                'status' => RfqResponseStatus::Rejected->value,
                'buyer_action_at' => now(),
                'updated_at' => now(),
            ]);
    }
}

