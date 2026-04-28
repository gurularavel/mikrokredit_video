<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    protected $fillable = ['name', 'logo', 'login', 'auth_key'];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
