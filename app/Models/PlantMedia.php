<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantMedia extends Model
{
    use HasFactory;
    protected $fillable = [
        'plant_id',
        'image_url',
    ];
}
