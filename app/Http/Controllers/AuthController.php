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
        $credetials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credetials)) {
            return redirect('/gebruikers');
        }

        return back()->with('error', 'het wachtwoord en of mail kloppen niet');
    }
    public function createAccount()
    {
        $Crebonrs = Crebo::all();

        return view('createAccount', compact('Crebonrs'));
    }

    public function createAccountPost(Request $request)
    {
        $guid = bin2hex(openssl_random_pseudo_bytes(16));

        $validatedData = $request->validate([
            'email' => 'required|email', // Example validation rules for email
            'role' => 'required',
        ], [
            'email.email' => 'Vul een geldig e-mailadres in',
            'email.required' => 'Het e-mailadres moet nog ingevuld worden',
            'role.required' => 'De rol moet geselecteerd worden',
            '*' => 'Deze velden moeten ingevuld worden',
        ]);

        $users = new User([
            'email' => $validatedData['email'],
        ]);

        //puur validatiecheck als de rol student is, dan zijn de crebo en of de crebo_nummer required
        if ($request->role == 0) {
            $validatedData = $request->validate([
                'crebo' => 'required'
            ], [
                'crebo.required' => 'Crebonummer moet nog geselecteerd worden'
            ]);
            if ($request->crebo == 'new') {
                $validatedData = $request->validate([
                    'new_crebo_number' => 'required'
                ], [
                    'new_crebo_number.required' => 'Nieuwe Crebonummer moet nog ingevuld worden'
                ]);
            }
        }

        $users->email = $request->email;
        $users->role = $request->role;
        $users->activation_key = $guid;

        if ($request->role == 0) {
            if (isset($request->new_crebo_number)) {
                $creboNumber = $request->new_crebo_number;

                $crebo = Crebo::firstOrNew(['name' => $creboNumber]);
                if (!$crebo->exists) {
                    $crebo->name = $request->new_crebo_number;
                    // The class doesn't exist, so save it
                    $crebo->save();
                } else {
                    return back()->withErrors(['error' => 'Deze crebo bestaat al']);
                }
            }
        }

        if ($request->role == 1) {
            // Validation: Check of de emailadres voor docent uniek is
            $validatedData = $request->validate([
                'email' => 'required|email|unique:users',
            ], [
                'email.unique' => 'Er is al een account voor een docent met dit e-mailadres',
            ]);

            if ($users->save()) {
                $role = "docent";

                try {
                    $this->Sendmail($users->email, $users->activation_key, $role);
                    Log::info('Email sent successfully to ' . $users->email);

                    // return back()->with('success', 'Er is een account gemaakt, en er is geprobeerd een mail te versturen naar ' . $users->email);
                } catch (\Exception $e) {
                    Log::error('Email sending failed: ' . $e->getMessage());
                    return back()->with('error', 'Er is een account gemaakt, maar er is een fout opgetreden bij het versturen van de activatiemail.');
                }
            } else {
                dd($users->errors());
            }
        } elseif ($request->role == 0) {
            // Check of de student-emailadres al bestaat
            $existingUser = User::where('email', $request->email)->first();

            if ($existingUser) {
                // als hij al bestaat
                $selectedUser = $existingUser->id;
                $existingCreboUser = CreboUser::where('user_id', $selectedUser)->where('crebo_id', $request->crebo)->first();


                if ($existingCreboUser) {
                    return back()->withErrors(['error' => 'Deze student zit al in deze crebo']);
                } else {
                    // dan wordt de user_id opgehaald op basis van ingevulde emailadres
                    $creboUser = new CreboUser([
                        'crebo_id' => $request->crebo,
                    ]);
                    $creboUser->user_id = $existingUser->id;
                    $creboUser->save();
                }
            } else { //anders wordt een nieuwe user aangemaakt
                if ($users->save()) {
                    if (($request->role == '0')) {
                        //als het niet een nieuwe crebo is
                        if (($request->crebo != 'new') && (isset($request->crebo))) {
                            // Controleer of de student al in de crebo zit
                            $lastUser = DB::table('users')->latest('id')->value('id');

                            $existingCreboUser = CreboUser::where('user_id', $lastUser)->where('crebo_id', $request->crebo)->first();

                            if ($existingCreboUser) {
                                return back()->withErrors(['error' => 'Deze student zit al in deze crebo']);
                            } else {
                                $creboUser = new CreboUser([
                                    'crebo_id' => $request->crebo,
                                ]);
                                $creboUser->crebo_id = $request->crebo;
                                $creboUser->user_id = $lastUser;
                                $creboUser->save();
                            }
                        } //anders wordt een nieuwe crebo toegevoegd
                        elseif (($request->crebo = 'new') && isset($request->new_crebo_number)) {
                            $creboUser = new CreboUser([
                                'crebo_id' => $request->crebo,
                            ]);
                            $lastUser = DB::table('users')->latest('id')->value('id');
                            $lastCrebo = DB::table('crebos')->latest('id')->value('id');
                            $creboUser->crebo_id = $lastCrebo;
                            $creboUser->user_id = $lastUser;
                            $creboUser->save();
                        }
                    }
                }

                if ($users->role == 0) {
                    $role = "student";
                } else {
                    $role = "docent";
                }

                try {
                    $this->Sendmail($users->email, $users->activation_key, $role);
                    Log::info('Email sent successfully to ' . $users->email);

                    // return back()->with('success', 'Er is een account gemaakt, en er is geprobeerd een mail te versturen naar ' . $users->email);
                } catch (\Exception $e) {
                    Log::error('Email sending failed: ' . $e->getMessage());
                    return back()->with('error', 'Er is een account gemaakt, maar er is een fout opgetreden bij het versturen van de activatiemail.');
                }
            }
        } else {
            dd($users->errors());
        }
        return redirect('gebruikers')->with('success', 'Gebruiker toegevoegd');
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

    
}
