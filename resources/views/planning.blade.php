@extends('layout/app')

@section('content')
<div id="users">
    <div class="header">
        <h1>Gebruikers</h1>
    </div>

    <div class="add-user">
        <a href="/createAccount"><i class="fa-solid fa-plus"></i></a>
    </div>

    @if (count($users) > 0)
        <div class="users">
                <div class="user">
                    <div class="info">
                        <p class="name">{{$user->firstname}} {{$user->lastname}}</p>
                        <div class="divider"></div>
                        <p>{{$user->email}}</p>
                        <div class="divider"></div>
                        @if ($user->role === 0)
                            <p>Schoonmaker</p>
                            <div class="divider"></div>
                        @elseif ($user->role === 1)
                            <p>Beheerder</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="user">
                    <div class="info">
                        <p>{{ $user->email }}</p>
                        <div class="divider"></div>
                        @if ($user->role === 0)
                            <p>Schoonmaker</p>
                        @elseif ($user->role === 1)
                            <p>Beheerder</p>
                        @endif
                    </div>
                    <div class="clock">
                        <i class="fa-regular fa-clock"></i>
                    </div> 
                </div>
            @endif
            @endforeach    
        </div>
    @else
        <div class="no-users">
            <p>Er zijn geen gebruikers gevonden</p>
        </div>
    @endif
</div>

@endsection