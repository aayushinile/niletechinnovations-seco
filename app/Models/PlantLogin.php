<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PlantLogin extends Authenticatable
{
    use Notifiable;
    protected $guard = 'plant_login';
    protected $table = 'plant_login';
    protected $fillable = [
        'manufacturer_id',
    ];
}
