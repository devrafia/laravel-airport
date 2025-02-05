<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_id',
        'class_type',
        'price',
        'total_seats'
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'flight_class_facility', 'flight_class_id', 'facility_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    protected static function booted()
    {
        static::created(function ($flightClass) {
            if ($flightClass->class_type == 'economy') {
                $row = 'E';
            } else {
                $row = 'B';
            }

            $totalSeats = $flightClass->total_seats;

            for ($i = 1; $i <= $totalSeats; $i++) {
                FlightSeat::create([
                    'flight_id' => $flightClass->flight_id, // Ambil flight_id dari relasi
                    'row' => $row,
                    'column' => $i,
                    'class_type' => $flightClass->class_type,
                    'is_available' => 1,
                ]);
            }
        });

        static::updated(function ($flightClass) {
            // Hapus kursi lama
            FlightSeat::where('flight_id', $flightClass->flight_id)->delete();

            // Tambahkan kursi baru berdasarkan total_seats terbaru
            $totalSeats = $flightClass->total_seats;

            for ($i = 1; $i <= $totalSeats; $i++) {
                FlightSeat::create([
                    'flight_id' => $flightClass->flight_id, // Ambil flight_id dari relasi
                    'row' => 'R',
                    'column' => $i,
                    'class_type' => $flightClass->class_type,
                    'is_available' => 1,
                ]);
            }
        });
    }
}
