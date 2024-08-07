<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MileSettings extends Model
{
    use HasFactory;
    protected $table = 'miles_settings';
    protected $fillable = ['type', 'miles'];
}
