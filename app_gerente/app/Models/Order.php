<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_id',
        'quantity',
        'menu_name',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getStatusNameAttribute()
    {
        return $this->status->name;
    }

}
