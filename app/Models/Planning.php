<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planning extends Model
{
    protected $table = 'planning';
    use HasFactory;

    protected $fillable = [
        'house_id',
        'element',
        'startdatetime',
        'enddatetime',
        'status',
    ];

    public function house()
    {
        return $this->belongsTo(House::class, 'house_id');
    }

    public function cleaners()
    {
        return $this->belongsToMany(User::class, 'planning_cleaner', 'planning_id', 'cleaner_id');
    }

    public function damage()
    {
        return $this->hasMany(Damage::class, 'planning_id');
    }

    public function decorations()
    {
        return $this->hasMany(Extradecoration::class);
    }
}
