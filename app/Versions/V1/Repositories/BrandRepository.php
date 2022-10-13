<?php

namespace App\Versions\V1\Repositories;

use App\Models\Brand;
use App\Traits\HasFilterFormFill;
use Illuminate\Database\Eloquent\Builder;

class BrandRepository
{
    use HasFilterFormFill;

    public function __construct(
        public Builder $builder
    ) {
        $this->builder = app(Brand::class)->query();
    }
}
