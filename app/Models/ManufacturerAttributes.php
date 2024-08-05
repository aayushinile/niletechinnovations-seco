<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturerAttributes extends Model
{
    use HasFactory;


    protected $table = 'manufacturer_attributes';

    protected $fillable = [
        'manufacturer_id',
        'attribute_name',
        'attribute_value',
        'attribute_type',
    ];
}
