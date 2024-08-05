<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Manufacturer extends Authenticatable
{
    use Notifiable;
    protected $guard = 'manufacturer';
    protected $table = 'manufacturer';
    protected $fillable = [
        'email', 'password',
    ];
}
