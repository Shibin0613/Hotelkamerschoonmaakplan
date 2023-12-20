<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Element extends Model
{
    protected $table = 'elements';
    use HasFactory;


    protected $fillable = [
        'name',
        'time',
    ];

    public function houses()
    {
        return $this->belongsToMany(House::class, 'house_elements');
    }

}
