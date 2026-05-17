<?php

namespace App\Repositories\ECommerce;

use App\DTOs\ECommerce\RfqResponseData;
use App\Domains\ECommerce\Enums\RfqResponseStatus;
use App\Domains\ECommerce\Models\RfqResponse;
use Illuminate\Database\Eloquent\Builder;

interface RfqResponseRepositoryInterface
{
    public function query(): Builder;

    public function upsertForSupplier(RfqResponseData $data): RfqResponse;

    public function markStatus(RfqResponse $response, RfqResponseStatus $status): RfqResponse;

    public function rejectOtherPendingForRfq(RfqResponse $acceptedResponse): void;
}

