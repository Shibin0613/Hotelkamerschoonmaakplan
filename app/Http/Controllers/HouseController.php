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
        $houses = DB::table('houses')->get();

        return view('houses',compact('houses'));
    }

    public function createHouse()
    {
        return view('createHouse');
    }

    public function createHousePost(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:houses', // Example validation rules for email
        ], [
            'name.unique' => 'Er is al een vakantiehuis/hotelkamer met deze naam',
            'name.required' => 'De naam is verplicht',
            '*' => 'Deze velden moeten ingevuld worden',
        ]);

        $elementsJson = json_encode($request->element);

        $house = new House([
            'name' => $validatedData['name'],
            'elements'=> $elementsJson,
        ]);
        if($house->save()){
            return back()->with('success', 'Vakantiehuis/Hotelkamer toegevoegd');
        }else{
            return back()->with('error', 'Het is niet gelukt');
        };
    }


    

    
}
