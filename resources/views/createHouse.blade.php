@extends('layout/app')

@section('content')
<div id="add-user">
    <div class="content-header">
        <h1>Vakantiehuis/Hotelkamer toevoegen</h1>
    </div>
    <div class="add-user">
        @if(Session::has('success'))
        <div class="success-message" role="alert">
            {{ Session::get('success') }}
        </div>
        @endif
        <form action="{{ route('createHousePost') }}" method="POST">
            @csrf
            <div class="input">
                <label for="name">Naam</label>
                <input type="text" name="name" id="naam" placeholder="Vul hier het vakantiehuis/hotelkamer in (Hotelkamer 301)" maxlength="255" required>
            </div>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input1">
                <label for="telefoon">Element</label>
                <label for="telefoon">Naam</label>
                <p><input type="tel" name="telefoon" id="telefoon" placeholder="Naam" maxlength="9"></p>
                <p><input type="tel" name="telefoon" id="telefoon" placeholder="Tijd" maxlength="9"></p>

            </div>
            @error('telefoon')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            
            <button>Account aanmaken</button>
        </form>
    </div>
</div>

@endsection