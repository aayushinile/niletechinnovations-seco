<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPropertyManagers extends Model
{
    use HasFactory;
    protected $table = 'community_property_managers';
    protected $fillable = [
        'community_id',
        'name',
        'designation',
        'image',
        'status',
        'phone',
        'email_id',
    ];
}
