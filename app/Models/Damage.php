<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Damage extends Model
{
    protected $table = 'damage';
    public $timestamps = false;
    use HasFactory;

    protected $fillable = [
        'planning_id',
        'name',
        'status',
        'need',
    ];

    public function planning()
    {
        return $this->belongsTo(Planning::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class,'id');
    }
}
