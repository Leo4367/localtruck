<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllTimeSlots extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'time_slot',
        'date_slot',
        'warehouse_id',
        'type',
        'status',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
