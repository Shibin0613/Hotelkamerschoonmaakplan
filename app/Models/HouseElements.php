<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HouseElements extends Model
{
    use HasFactory;
    protected $table = 'house_elements';

    protected $fillable = [
        'house_id',
        'element_id'
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function elements()
    {
        return $this->belongsTo(Element::class);
    }
}
