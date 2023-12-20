@extends('layout/app')

@section('content')
<div id="houses">
    <div class="header">
        <h1>Vakantiehuis/Hotelkamer</h1>
    </div>

    <div class="add-house">
        <a href="/createHouse"><i class="fa-solid fa-plus"></i></a>
    </div>

    @if (count($houses) > 0)
        <div class="houses">
            @foreach ($houses as $house)
            <div class="house">
                <div class="info">
                    <p class="name">{{$house->name}}</p>
                    <div class="divider"></div>
                    <p>{{count($house->elements)}} elements</p>
                    <div class="divider"></div>
                </div>
                <div class="right">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <i class="fa-solid fa-trash-can"></i>
                </div>
            </div>
            @endforeach    
        </div>
    @else
        <div class="no-houses">
            <p>Er zijn geen huizen gevonden</p>
        </div>
    @endif
</div>

@endsection