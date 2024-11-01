<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DateManage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'forbidden_date',
        'warehouse_id',
        'type',
        'status',
    ];
    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
