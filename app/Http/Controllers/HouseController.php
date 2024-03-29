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

    public function editHouse($houseId)
    {

        $houseNotFind = House::find($houseId);

        if (!$houseNotFind) {
            //Voor het geval als de planning niet te vinden is
            return back()->with('error', 'Vakantiehuis/hotelkamer is niet te vinden.');
        }
        
        $house = DB::table('houses')
        ->where('id', $houseId)
        ->get();
        
        return view('editHouse', compact('house'));
        }
    
    public function updateHouse(Request $request, $houseId)
    {
        $house = House::find($houseId);
        
        if(empty($request->name))
        {
           $elementsJson = json_encode($request->element);
           $house->name = $house->name;
           $house->elements = $elementsJson;
           if($house->update())
           {
                return back()->with('success', 'Vakantiehuis/Hotelkamer bijgewerkt');
           }
           else
           {
                return back()->with('error', 'Het is niet gelukt');
           }
        }else{
            $validatedData = $request->validate([
                'name' => 'string|unique:houses', // Example validation rules for email
            ], [
                'name.unique' => 'Er is al een vakantiehuis/hotelkamer met deze naam',
            ]);

            $elementsJson = json_encode($request->element);
            $house->name = $request->name;
            $house->elements = $elementsJson;
            if($house->update())
            {
                return back()->with('success', 'Vakantiehuis/Hotelkamer bijgewerkt');
            }
            else
            {
                return back()->with('error', 'Het is niet gelukt');
            }
        }
    }
    
    public function deleteHouse($houseId)
    {
        $house = House::find($houseId);

        if (!$house) {
          return response()->json(['error' => 'Vakantiehuis/hotelkamer is niet te vinde!'], 404);
        }

        // Check if de house is gelinkt met andere planning
        $housePlannings = $house->plannings;

        $authorized = false;
        foreach ($housePlannings as $housePlanning) {
          if ($house->id === $housePlanning->id) {
            $authorized = true;
            break;
          }
        }

        // Delete related planning->damage->extradecoration
        $house->plannings()->each(function ($plannings) {
            $plannings->damage()->delete();
            $plannings->decorations()->delete();
            $plannings->cleaners()->detach();
            $plannings->delete();
        });
    
        // Delete the template
        $house->delete();

        return back()->with(['success' => 'Vakantiehuis/hotelakmer is verwijderd']);
    }

}
