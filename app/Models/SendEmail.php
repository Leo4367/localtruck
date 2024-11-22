<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendEmail extends Model
{
    use HasFactory;

    /*protected $fillable = [
        'user_id',
        'customer_name',
        'company_name',
        'address',
        'email',
        'work_order',
        'cornerstone',
        'tlx',
        'tql',
        'spread',
    ];*/
    protected $fillable = [
        'user_id',
        'data',
        'message',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function getColumnNameAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setColumnNameAttribute($value)
    {
        $this->attributes['data'] = json_encode(array_values($value));
    }

    public function user()
    {
        return $this->belongsTo(Administrator::class, 'user_id');
    }
}
