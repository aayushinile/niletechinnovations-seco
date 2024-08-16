<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;
    protected $table = 'community';

    protected $fillable = [
        'community_name',
        'mobile',
        'email',
        'community_address',
        'city',
        'state',
        'zipcode',
        'image',
        'description',
        'user_id',
        'no_of_lots',
        'no_of_new_homes',
        'vacant_lots',
        'no_of_home_needed',
        'homes_needed_per_year',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'image' => 'array',
        'property_management' => 'array',
    ];
}
