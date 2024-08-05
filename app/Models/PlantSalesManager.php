<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantSalesManager extends Model
{
    use HasFactory;
    protected $table = 'plant_sales_manager';

    protected $fillable = [
        'plant_id',
        'name',
        'email',
        'designation',
        'phone',
        'manufacturer_id',
        'image',
    ];
}
