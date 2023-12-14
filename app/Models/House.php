<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    protected $table = 'houses';
    use HasFactory;


    protected $fillable = [
        'name',
    ];

    public function elements(): HasMany 
    {
        return $this->hasMany(House::class);
    }

    public function plannings(): HasMany 
    {
        return $this->hasMany(Planning::class);
    }
}
