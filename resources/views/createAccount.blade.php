@extends('layout/app')

@section('content')
<div id="add-user">
    <div class="content-header">
        <h1>Gebruiker toevoegen</h1>
    </div>
    <div class="add-user">
        @if(Session::has('success'))
        <div class="success-message" role="alert">
            {{ Session::get('success') }}
        </div>
        @endif
        <form action="{{ route('createAccountPost') }}" method="POST">
            @csrf
            <div class="input">
                <label for="email">E-mailadres</label>
                <input type="email" name="email" id="email" placeholder="Vul hier het e-mailadres in..." maxlength="255" required>
            </div>
            @error('email')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input1">
                <label for="telefoon">Telefoonnr</label>
                <p>+31<input type="tel" name="telefoon" id="telefoon" placeholder="Vul hier de telefoonnr in... Format: 1234 5678 9" maxlength="9"></p>
            </div>
            @error('telefoon')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            
            <button>Account aanmaken</button>
        </form>
    </div>
</div>

@endsection