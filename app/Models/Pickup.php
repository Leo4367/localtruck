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
        'pickup_number',
        'driver_name',
        'phone_number',
        'time_slot',
        'warehouse_id',
    ];

    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }

    // 定义与 Appointment 的反向关系
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointments_id');
    }
}
