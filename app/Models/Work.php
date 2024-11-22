<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = ['customer_name', 'address', 'work_order'];

    public function inquiries()
    {
        return $this->hasMany(InquiryPrice::class, 'work_id');
    }
}

