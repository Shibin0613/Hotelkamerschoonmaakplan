@extends('layout/app')

@section('content')
    <div id="users">
        @if (Session::has('success'))
            <div class="success-popup" role="alert">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::has('error'))
            <div class="error-popup" role="alert">
                {{ Session::get('error') }}
            </div>
        @endif

        <div class="header">
            <h1>Planning</h1>
        </div>

        <a class="add-user" href="/createAccount"><i class="fa-solid fa-plus"></i></a>

        
    </div>
@endsection
