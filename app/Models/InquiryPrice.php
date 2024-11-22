<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryPrice extends Model
{
    protected $table = 'inquiry_price';
    protected $fillable = ['work_id', 'broker_id', 'price'];

    public function work()
    {
        return $this->belongsTo(Work::class, 'work_id');
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class, 'broker_id');
    }
}

