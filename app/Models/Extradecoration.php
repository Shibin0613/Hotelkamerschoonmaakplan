<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Extradecoration extends Model
{
    protected $table = 'extradecoration';
    public $timestamps = false;
    use HasFactory;


    protected $fillable = [
        'planning_id',
        'name',
        'time',
    ];

    public function planning()
    {
        return $this->belongsTo(Planning::class);
    }
    
}
