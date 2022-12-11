<?php

namespace App\Versions\V1\Http\Controllers\Api;

use App\Models\VehicleClass;
use App\Versions\V1\Http\Resources\Collections\OfferCollection;
use App\Versions\V1\Http\Controllers\Controller;
use App\Versions\V1\Http\Resources\Collections\VehicleClassCollection;
use App\Versions\V1\Http\Resources\VehicleClassResource;
use App\Versions\V1\Repositories\OfferRepository;
use App\Versions\V1\Repositories\VehicleClassRepository;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function __construct(
        public OfferRepository $repository,
        public VehicleClassRepository $classRepository,
    ) {
    }

    public function index(Request $request): OfferCollection
    {
        $this->repository->getOffer()->filterBy($request->all());

        return new OfferCollection($this->repository->paginate());
    }

    public function groupedByClass(Request $request): VehicleClassResource
    {
        return new VehicleClassResource(
            $this->classRepository->certainWithOffers(VehicleClass::PER_GROUP)
        );
    }
}
