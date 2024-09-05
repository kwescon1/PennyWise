<?php

namespace App\Models;

use App\Enums\Auth\OtpType;
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
    ];
}
