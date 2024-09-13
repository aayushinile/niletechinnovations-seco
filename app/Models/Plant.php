<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;
    protected $table = 'plant';

    protected $fillable = [
        'plant_name',
        'email',
        'phone',
        'description',
        'full_address',
        'city',
        'state',
        'country',
        'zipcode',
        'price_range',
        'type',
        'specification',
        'manufacturer_id',
        'latitude',
        'longitude',
        'shipping_cost',
        'from_price_range',
        'to_price_range',
        'web_link',
        'status',
        'is_approved'
    ];



    public function plantLogin()
{
    return $this->belongsTo(PlantLogin::class, 'manufacturer_id');
}
}
