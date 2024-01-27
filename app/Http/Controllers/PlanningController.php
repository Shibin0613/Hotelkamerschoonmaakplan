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
        $events = [];
        $user = Auth::user();
        if($user->role === 1)
        {
            $planningen = Planning::with('house','cleaners','damage')
            ->get();
            
            foreach($planningen as $planning)
            {
                $events[] = [
                    'title' => $planning->house->name,
                    'start' => $planning->startdatetime,
                    'end' => $planning->enddatetime,
                ];
            }
        }
        elseif($user->role === 0)
        {
            $planningen = Planning::with('house', 'cleaners', 'damage')
            ->whereHas('cleaners', function ($query) use ($user) {
                $query->where('cleaner_id', $user->id);
            })
            ->get();

            foreach ($planningen as $planning) {
                $cleanerNames = $planning->cleaners->pluck('firstname')->pluck('lastname')->toArray();
            
                $events[] = [
                    'title' => [
                        $planning->house->name,
                        implode(', ', $cleanerNames),
                        '',
                    ],
                    'start' => $planning->startdatetime,
                    'end' => $planning->enddatetime,
                ];
            }
        }
        
        return view('planning', compact('user','planningen','events'));
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
            'startdatetime' => 'required|date_format:Y-m-d H:i:s',
            'enddatetime' => 'required|date_format:Y-m-d H:i:s',
        ], [
            'startdatetime.datetime' => 'Vul in als waarde: datum tijd',
            'enddatetime.datetime' => 'Vul in als waarde: datum tijd',
            'house.exists' => 'Dit vakantiehuis/hotelkamer bestaat niet',
            '*' => 'Deze velden moeten ingevuld worden',
        ]);

        if ($request->input('enddatetime') < $request->input('startdatetime')) {
            return back()->with('error', 'Vul een geldig einddatum en tijd in');
        }

        $jsonelements = json_encode($request->selected_elements);
        
        $planning = new Planning([
            'house_id' => $request->house,
            'element' => $jsonelements,
            'startdatetime' => $request->startdatetime,
            'enddatetime' => $request->enddatetime,
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

        if ($request->input('enddatetime') < $request->input('startdatetime')) {
            return back()->with('error', 'Vul een geldig einddatum en tijd in');
        }
        
        if(isset($request->house)){
            $request->validate([
                'house' => 'numeric|exists:houses,id',
                'startdatetime' => 'required|date_format:Y-m-d H:i:s',
                'enddatetime' => 'required|date_format:Y-m-d H:i:s',
            ], [
                'startdatetime.datetime' => 'Vul in als waarde datum tijd',
                'enddatetime.datetime' => 'Vul in als waarde datum tijd',
                'house.exists' => 'Dit vakantiehuis/hotelkamer bestaat niet',
            ]);
            $selected_elements = json_encode($request->selected_elements);

            $planning->house_id = $request->house;
            $planning->element = $selected_elements;
        }
        $decorations = json_encode($request->decoration);

        $planning->startdatetime = $request->startdatetime;
        $planning->enddatetime = $request->enddatetime;

        if($planning->update()){
            return back()->with('success', 'Planning bijgewerkt');
        }
        else{
            return back()->with('error', 'Het is niet gelukt');
        }

    }
    
    public function updatedPlanning()
    {
        
    }
    
}
