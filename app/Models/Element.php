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
        'house_id',
        'name',
        'time',
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }

}
