<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'flight_id',
        'flight_class_id',
        'name',
        'email',
        'phone',
        'number_of_passengers',
        'promo_code_id',
        'payment_status',
        'subtotal',
        'grandtotal',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function class()
    {
        return $this->belongsTo(FlightClass::class);
    }

    public function promo()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function passengers()
    {
        return $this->hasMany(TransactionPassenger::class);
    }

    public static function generateCode()
    {
        do {
            $code = 'TRX-' . strtoupper(uniqid());
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Boot the model and set the code field automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->code)) {
                $transaction->code = self::generateCode();
            }
        });
    }
}
