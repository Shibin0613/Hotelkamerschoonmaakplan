<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Damage extends Model
{
    protected $table = 'damage';
    use HasFactory;
    

    protected $fillable = [
        'planning_id',
        'name',
        'status',
        'need',
        'repair',
        'datetime',
    ];

    public function planning()
    {
        return $this->belongsTo(Planning::class);
    }

}
