<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_id',
        'container_number',
        'driver_name',
        'phone_number',
        'time_slot',
        'warehouse_id',
    ];
    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
}