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
        @if (Session::has('error'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('error') }}
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
            
            <button>Account aanmaken</button>
        </form>
    </div>
</div>

@endsection