<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specifications extends Model
{
    use HasFactory;
    protected $table = 'specifications';

    protected $fillable = [
        'name',
        'values',
        'manufacturer_id',
        'plant_id',
        'status',
        'image'
    ];
}