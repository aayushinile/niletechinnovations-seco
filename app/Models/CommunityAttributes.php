<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityAttributes extends Model
{
    use HasFactory;
    protected $table = 'community_attributes';
    protected $fillable = [
        'community_id',
        'attribute_type',
        'attribute_name',
        'value',
    ];
}
