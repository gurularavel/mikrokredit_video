<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'username',
        'password',
        'name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
