<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchaser extends Model
{

    protected $fillable = ['customer_name', 'address', 'work_order'];

    public function inquiries()
    {
        return $this->hasMany(InquiryPrice::class, 'purchaser_id');
    }
}
