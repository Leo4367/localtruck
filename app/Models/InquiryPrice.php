<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryPrice extends Model
{
    protected $table = 'inquiry_price';
    protected $fillable = ['purchaser_id', 'broker_id', 'price'];

    public function purchaser()
    {
        return $this->belongsTo(Purchaser::class, 'purchaser_id');
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class, 'broker_id');
    }
}

