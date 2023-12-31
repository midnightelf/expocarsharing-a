<?php

namespace App\Filters\Offer;

use App\Filters\FilterContract;
use Illuminate\Database\Eloquent\Builder;

class BrandsFilter extends FilterContract
{
    public function handle(string|array $value): void
    {
        $this->query->whereHas('vehicle', function (Builder $query) use ($value) {
            $query->whereHas('brand', function (Builder $query) use ($value) {
                $query->whereIn('id', $value);
            });
        });
    }
}
