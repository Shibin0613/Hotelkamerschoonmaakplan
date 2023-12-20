<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Damage;
use App\Models\Element;
use App\Models\Extradecoration;
use App\Models\House;
use App\Models\HouseElements;
use App\Models\Planning;
use App\Models\PlanningCleaner;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HouseController extends Controller
{
    public function houses()
    {
        $houses = House::with('elements')->get();

        return view('houses',compact('houses'));
    }

    public function createHouse()
    {
        return view('createHouse');
    }

    public function createHousePost()
    {
        return view('createHouse');
    }


    

    
}
