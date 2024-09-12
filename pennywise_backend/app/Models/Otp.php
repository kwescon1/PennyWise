<?php

namespace App\Models;

use App\Enums\Auth\OtpType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    // Define constants for status
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        "type" => OtpType::class,
        "expires_at" => "datetime",
    ];

    /**
     * Scope to get active OTPs of a certain type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveOtps(Builder $query, OtpType $type): Builder
    {
        return $query->whereIsActive(self::STATUS_ACTIVE)->whereType($type);
    }

    /**
     * Scope a query to only include records created within a time frame.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $minutes  The number of minutes within which records should have been created.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent(Builder $query, $minutes): Builder
    {
        return $query->where('created_at', '>=', $minutes);
    }

    /**
     * Scope to get OTPs that have not expired.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }
}
