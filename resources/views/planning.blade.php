@extends('layout/app')

@section('content')
<div id="users">
    <div class="header">
        <h1>Planning</h1>
    </div>

    <div class="add-user">
        <a href="/createPlanning"><i class="fa-solid fa-plus"></i></a>
    </div>

    <div id="calendar"></div>

  
    
        <div class="no-users">
            <p>Er staan geen planning</p>
        </div>
    
</div>

@endsection