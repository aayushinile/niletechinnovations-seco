<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactManufacturer extends Model
{
    use HasFactory;

    protected $table = 'contact_manufacturer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'user_id',
        'manufacturer_id',
        'message',
        'plant_id',
        'location',
        'email',
        'phone_no',
        'status',
    ];
}
