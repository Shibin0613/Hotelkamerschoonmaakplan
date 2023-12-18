<?php

namespace App\Http\Controllers;

use App\Mail\Activation;
use App\Models\User;
use App\Models\Planning;
use App\Models\PlanningCleaner;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SebastianBergmann\Type\NullType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

use function Laravel\Prompts\error;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loginPost(Request $request)
    {
        $inputs = [
            'username' => $request->gebruikersnaam,
            'password' => $request->password,
        ];

        if (Auth::attempt($inputs)) {
            return redirect('/planning');
        }

        return back()->with('error', 'de gebruikersnaam en of het wachtwoord kloppen niet');
    }
    public function createAccount()
    {
        return view('createAccount');
    }

    public function createAccountPost(Request $request)
    {
        $guid = bin2hex(openssl_random_pseudo_bytes(16));
        
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users', // Example validation rules for email
            'telefoon' => 'digits:9',
        ], [
            'email.unique' => 'Er is al een account met dit e-mailadres',
            'email.email' => 'Vul een geldig e-mailadres in',
            'email.required' => 'Het e-mailadres is verplicht',
            'telefoon.digits' => 'vul een geldig telefoonnr in',
            '*' => 'Deze velden moeten ingevuld worden',
        ]);
        
        $user = new User([
            'email' => $validatedData['email'],
            'telefoonnr' =>$validatedData['telefoon']
        ]);

        $user->email = $request->email;
        $user->role = 0;
        $user->telefoonnr = $request->telefoon;
        $user->activation_key = $guid;

        if ($user->save()) {

            return back()->with('success', 'Er is een account gemaakt, en er is geprobeerd een mail te versturen naar ' . $user->email);
        }else{
            dd($user->errors());
        }
        
    }

    public function sendMail($email, $code, $rol)
    {
        Mail::to($email)->send(new Activation($code, $rol));
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }


    public function viewActivateAccount(request $request)
    {
        //haal de activatiecode van query
        $activationCode = $request->query('code');

        //check of de actvatiecode bestaat
        $user = User::where('activation_key', $activationCode)->first();
        if ($user == null) {
            return ("Gebruiker bestaat niet");
            return error("Gebruiker bestaat niet");
        } elseif ($user->password != null) {
            return ("Account is al geactiveerd!");
            return error("Account is al geactiveerd!");
        } else {
            return view('activateAccount');
        }
    }

    public function activateAccountPost(request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|min:2|max:255', // Example validation rules for voornaam
            'lastname' => 'required|string|min:2|max:255',
            'password' => 'required|string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_]).+$/',
        ], [
            'firstname.required' => 'De voornaam moet nog ingevuld worden',
            'lastname.required' => 'De achternaam moet nog ingevuld worden',
            'password.required' => 'Wachtwoord moet nog ingevuld worden',
            'password.regex' => 'Het wachtwoord moet ten minste 8 tekens bevatten, waaronder minimaal 1 kleine letter, 1 hoofdletter, 1 cijfer en 1 speciaal teken',
            'password.min' => 'Het wachtwoord moet ten minste 8 tekens bevatten, waaronder minimaal 1 kleine letter, 1 hoofdletter, 1 cijfer en 1 speciaal teken',
            '*' => 'Deze velden moeten ingevuld worden',
        ]);

        //haal de activatiecode van query
        $activationCode = $request->query('code');

        $user = User::where('activation_key', $activationCode)->first();

        if ($user->update([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'password' => Hash::make($request->input('password')), // Hash the new password
        ])) {
            auth()->login($user);

            return redirect('/'); // terugsturen naar login pagina
        }

        return back()->withErrors('Account activeren mislukt');
    }
    
    public function gebruikers()
    {
        $role0Users = DB::table('users')
            ->where('role', 0)
            ->whereNotNull('password')
            ->orderBy('firstname')
            ->get();

        $emptyPasswordUsers = DB::table('users')
            ->whereNull('password')
            ->get();

        $users = $role0Users->concat($emptyPasswordUsers);

        return view('users', compact('users'));
    }

    
}
