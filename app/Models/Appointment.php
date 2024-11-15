<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'pickup_number',
        'phone_number',
        'driver_name',
        'time_slot',
        'warehouse_id',
        'type',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function pickup()
    {
        return $this->hasOne(Pickup::class, 'appointments_id');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'appointments_id');
    }

}
