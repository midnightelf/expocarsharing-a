<?php

namespace App\Models;

use App\Traits\HasVehicle;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $status
 * @property-read int $offer_id
 * @property-read int $user_id
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Offer $offer
 * @property-read \App\Models\Vehicle $vehicle
 * @property-read \Carbon\Carbon $started_at
 * @property-read \Carbon\CarbonInterval $active_in
 * @property-read \Carbon\Carbon|null $finished_at
 * @property-read \Carbon\Carbon|null $created_at
 * @property-read \Carbon\Carbon|null $updated_at
 * @property-read \Carbon\Carbon|null $deleted_at
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Order extends Model
{
    /**
     * 3 бургера с 1ой куриной котлетой
     * 2 стрипсы в стаканчике + 2 сырных соуса
     * картошка фри в тарелке + 2 сырных соуса 1 томатный
     */
    use HasFactory;
    use HasVehicle;
    use SoftDeletes;

    // FIXME: Refactor to enums
    public const STATUS_RESERVED   = 'RESERVED';
    public const STATUS_RENTED     = 'RENTED';
    public const STATUS_CONFIRMING = 'CONFIRMING';
    public const STATUS_BROKEN     = 'BROKEN';
    public const STATUS_ERROR      = 'ERROR';

    public static array $statuses = [
        self::STATUS_RESERVED   => 'ожидание доставки',
        self::STATUS_RENTED     => 'в аренде',
        self::STATUS_CONFIRMING => 'подтверждение оплаты',
        self::STATUS_BROKEN     => 'сломано',
        self::STATUS_ERROR      => '¯\_(ツ)_/¯',
    ];

    protected $fillable = [
        'status',
        'finised_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'active_in',
    ];

    public function getActiveInAttribute(): CarbonInterval
    {
        $started_at = Carbon::parse($this->started_at);

        return $started_at->diffAsCarbonInterval(Carbon::now());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function vehicle(): HasOneThrough
    {
        return $this->hasOneThrough(Vehicle::class, Offer::class, 'id', 'id', 'offer_id', 'vehicle_id');
    }
}
