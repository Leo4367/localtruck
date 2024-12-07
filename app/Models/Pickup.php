<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pickup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointments_id',
        'appt_number',
        'driver_name',
        'phone_number',
        'time_slot',
        'warehouse_id',
        'po_number',
        'dock_number',
        'user_id',
        'vehicle_type',
    ];

    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
}
