@extends('layout/app')

@section('content')
<div id="houses">
    <div class="header">
        <h1>Vakantiehuis/Hotelkamer</h1>
    </div>

    <div class="add-houses">
        <a href="/createHouse"><i class="fa-solid fa-plus"></i></a>
    </div>


        <div class="houses">
            
                <div class="house">
                    <div class="info">
                        <p class="name"></p>
                        <div class="divider"></div>
                        <p></p>
                        <div class="divider"></div>
                    </div>
                </div>
        </div>

        <div class="no-houses">
            <p>Er zijn geen gebruikers gevonden</p>
        </div>

</div>

@endsection