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

            $jsonArray = [];
            foreach ($planningen as $planning) {
                $cleanerNames = $planning->cleaners->pluck('firstname')->pluck('lastname')->toArray();
                
                $jsonArray[$planning->id] = [
                    'name' => $planning->element,
                    'time' => $planning->time,
                    // Add other properties as needed
                ];

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
            'startdatetime' => 'required',
            'enddatetime' => 'required|after:startdatetime',
        ], [
            'enddatetime.after' => 'Vul een geldige einddatum tijd',
            'house.exists' => 'Dit vakantiehuis/hotelkamer bestaat niet',
        ]);

        $jsonelements = json_encode($request->selected_elements);
        $planning = new Planning([
            'house_id' => $request->house,
            'element' => $jsonelements,
            'startdatetime' => $request->startdatetime,
            'enddatetime' => $request->enddatetime,
            'status' => 1,
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
            foreach($request->decoration as $decoration){
                if($decoration['name'] !== null && $decoration['time'] !== null){
                    $decoration = new Extradecoration([
                        'planning_id' => $planningid,
                        'name' => $decoration['name'],
                        'time' => $decoration['time'],
                    ]);
                
                    $decoration->save();
                }
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
        if(isset($request->decoration)){
            $decorations = json_encode($request->decoration);
        }

        $planning->startdatetime = $request->startdatetime;
        $planning->enddatetime = $request->enddatetime;

        if($planning->update()){
            return back()->with('success', 'Planning bijgewerkt');
        }
        else{
            return back()->with('error', 'Het is niet gelukt');
        }

    }
    
    public function updatedPlanning(Request $request, $planningId)
    {
        $planning = Planning::find($planningId);

        if (!$planning) {
            //Voor het geval als de planning niet te vinden is
            return redirect('planning')->with('error', 'Planning is niet te vinden.');
        }

        // alle requestdata ophalen
        $formData = $request->all();

        // haal alleen de elements data eruit
        $elements = $formData['elements'];

        // Check of alle element aan staat, en dat betekent dat een taak klaar is
        $allOn = true;
        foreach ($elements as $element) {
            if (!isset($element['onoff']) || $element['onoff'] !== 'on') {
                $allOn = false;
                break;
            }
        }
        // Check of de damage input null is, als wel, dat is die planning afgerond zonder schade, anders wel 
        $hasDamage = $request->input('damage') !== null;
        // Update planning status
        if($allOn && !$hasDamage){
            $status = 0;
        }elseif($allOn && $hasDamage){
            $status = 2;
        }elseif(!$allOn){
            $status = 1;
        }
        
        $elementsJson = json_encode($request->elements);
        $planning->element = $elementsJson;
        $planning->status = $status;

        if($hasDamage){
            $damage = new Damage([
                'planning_id' => $planningId,
                'name' => $request->damage,
                'status' => 1,
                'need' => 1,
            ]);
            $damage->save();
        }

        if($planning->update())
        {
            return back()->with('success', 'Planning is bijgewerkt');
        }
        else
        {
            return back()->with('error', 'Het is niet gelukt');
        }


    }

    public function damage()
    {
        $damages = Damage::with('planning', 'house')
        ->orderByDesc('status')
        ->get();

        foreach($damages as $damage){
            $house_id = $damage->planning->house_id;
        }
        $houses = DB::table('houses')
        ->where('id',$house_id)
        ->get();

        return view('damages', compact('damages','houses'));
    }

    public function updateDamage()
    {
        
    }
    
}
