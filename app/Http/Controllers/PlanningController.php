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
        
        return view('planning',compact('user'));
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

        
        foreach($users as $user)
        {
            dd($user->planning->status);
        }

        foreach($houses as $house){
            $elements[$house->id] = json_decode($house->elements);
            
        }

        return view('createPlanning', compact('houses','cleaners','elements'));
    }

    public function createPlanningPost(Request $request)
    {
        $validatedData = $request->validate([
            
        ], [
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
            return back();
        }

        
            
    }

    
}
