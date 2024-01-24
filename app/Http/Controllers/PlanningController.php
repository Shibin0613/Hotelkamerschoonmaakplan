<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Damage;
use App\Models\Element;
use App\Models\Extradecoration;
use App\Models\House;
use App\Models\Planning;
use App\Models\PlanningCleaner;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlanningController extends Controller
{
    public function planning()
    {
        $user = Auth::user();

        $planningen = Planning::with('house','cleaners','damage')
        ->get();
        
        return view('planning', compact('user','planningen'));
    }

    public function createPlanning()
    {
        $houses = House::all();
        $cleaners = DB::table('users')
        ->where('role', 0)
        ->whereNotNull('password')
        ->get();


        $users = User::with('planning')
            ->where('role', 0)
            ->whereHas('planning', function ($query) {
                $query->where('status', 0);
            })
            ->get();

        foreach($houses as $house){
            $elements[$house->id] = json_decode($house->elements);
            
        }

        return view('createPlanning', compact('houses','cleaners','elements'));
    }

    public function createPlanningPost(Request $request)
    {
        $validatedData = $request->validate([
            'house' => 'required|numeric|exists:houses,id',
            'datetime' => 'required|date_format:Y-m-d H:i:s',
        ], [
            'datetime.datetime' => 'Vul in als waarde: datum tijd',
            'house.exists' => 'Dit vakantiehuis/hotelkamer bestaat niet',
            '*' => 'Deze velden moeten ingevuld worden',
        ]);

        $jsonelements = json_encode($request->selected_elements);
        
        $planning = new Planning([
            'house_id' => $request->house,
            'element' => $jsonelements,
            'datetime' => $request->datetime,
            'status' => 0,
        ]);

        if($planning->save()){
            $planningid = DB::table('planning')
            ->latest('id')
            ->value('id');

            foreach ($request->schoonmakers as $cleaner) {
                $planningCleaner = new PlanningCleaner([
                    'planning_id' => $planningid,
                    'cleaner_id' => $cleaner,
                ]);
            
                $planningCleaner->save();
            }

            foreach ($request->decoration as $decoration) {
                $decoration = new Extradecoration([
                    'planning_id' => $planningid,
                    'name' => $decoration['name'],
                    'time' => $decoration['time'],
                ]);
            
                $decoration->save();
            }
            return back()->with('success', 'Planning is aangemaakt');
        }else{
            return back()->with('error', 'Planning is niet aangemaakt.');

        }  
    }

    public function editPlanning($planningId)
    {
        $planning = Planning::with('house', 'cleaners','decorations')
        ->where('id', $planningId)
        ->get();

        $planningNotFind = Planning::find($planningId);

        if (!$planningNotFind) {
            //Voor het geval als de planning niet te vinden is
            return back()->with('error', 'Planning is niet te vinden.');
        };
        
        $cleanersNotInPlanning = User::whereDoesntHave('planning')
        ->whereNotNull('password')
        ->where('role', 0)
        ->get();

        $houses = House::all();
        
        $cleaners = User::with('planning')
        ->where('role', 0)
        ->whereNotNull('password')
        ->get();

        foreach($houses as $house){
            $elements[$house->id] = json_decode($house->elements);
        }

        return view('editPlanning', compact('cleanersNotInPlanning','planning','houses','cleaners','elements'));
    }

    public function updatePlanning(Request $request, $planningId)
    {
        $planning = Planning::find($planningId);
        if (!$planning) {
            //Voor het geval als de planning niet te vinden is
            return redirect('planning')->with('error', 'Planning is niet te vinden.');
        }
        
        if(isset($request->house)){
            $request->validate([
                'house' => 'numeric|exists:houses,id',
                'datetime' => 'required|date_format:Y-m-d H:i:s',
            ], [
                'datetime.datetime' => 'Vul in als waarde datum tijd',
                'house.exists' => 'Dit vakantiehuis/hotelkamer bestaat niet',
            ]);
            $selected_elements = json_encode($request->selected_elements);

            $planning->house_id = $request->house;
            $planning->element = $selected_elements;
        }
        $decorations = json_encode($request->decoration);

        $planning->datetime = $request->datetime;

        if($planning->update()){
            return back()->with('success', 'Planning bijgewerkt');
        }
        else{
            return back()->with('error', 'Het is niet gelukt');
        }

        
    }

    
}
