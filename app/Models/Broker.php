<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broker extends Model
{
    protected $fillable = ['company_name', 'broker_name', 'email'];

    public function inquiries()
    {
        return $this->hasMany(InquiryPrice::class, 'broker_id');
    }
}
