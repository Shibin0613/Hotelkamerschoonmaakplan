@extends('layout/app')

@section('content')
<div id="users">
    <div class="header">
        <h1>Planning</h1>
    </div>

    <div class="add-user">
        <a href="/createAccount"><i class="fa-solid fa-plus"></i></a>
    </div>

  
        <div class="users">
                <div class="user">
                    <div class="info">
                        <p class="name"></p>
                        <div class="divider"></div>
                        <p></p>
                        <div class="divider"></div>

                    </div>
                </div>
            
                <div class="user">
                    <div class="info">
                        <p></p>
                        <div class="divider"></div>

                    </div>
                    <div class="clock">
                        <i class="fa-regular fa-clock"></i>
                    </div> 
                </div>

        </div>
    
        <div class="no-users">
            <p>Er zijn geen gebruikers gevonden</p>
        </div>
    
</div>

@endsection