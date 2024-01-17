<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanningCleaner extends Model
{
    use HasFactory;

    protected $table = 'planning_cleaner';
    public $timestamps = false;

    protected $fillable = [
        'planning_id',
        'cleaner_id',
    ];
    
    public function cleaner()
    {
        return $this->belongsTo(User::class);
    }

    public function planning()
    {
        return $this->belongsTo(Planning::class);
    }

}
