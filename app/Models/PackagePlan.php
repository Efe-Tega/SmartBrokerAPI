<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagePlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
    ];
}
