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
                    @if ($house->elements !== Null)
                        <p>{{ count(json_decode($house->elements, true) ?? []) }} elementen</p>
                    @else
                        <p>0 element</p>
                    @endif
                    <p></p>
                    <div class="divider"></div>
                </div>
                <div class="right">
                    <a href="{{ route('editHouse', ['houseId' => $house->id]) }}"><i class="fa-solid fa-pen-to-square"></i></a>
                    <form id="delete-house-{{$house->id}}" class="delete-house" method="post" action="{{ route('houses.deleteHouse', ['houseId' => $house->id]) }}" title="Verwijder house">
                        @csrf
                        @method('delete')
                        <button type="button" data-form-id="{{$house->id}}">
                            <i class="far fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach    
        </div>
    @else
        <div class="no-houses">
            <p>Er zijn geen huizen gevonden</p>
        </div>
    @endif


    <div id="delete-house-modal">
        <div class="modal-content">
            <div class="close-btn">
                <button type="button"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <h2>Weet je zeker dat je dit vakantiehuis/hotelkamer wilt verwijderen?</h2>
            <div class="buttons">
                <button class="button">Verwijderen</button>
                <button class="button">Annuleren</button>
            </div>
        </div>
    </div>

</div>

<script>
// Attach click event to the delete button
$('.delete-house button').on('click', function () {
        var formId = $(this).data('form-id');
        $('#delete-house-modal').data('form-id', formId);
        $('#delete-house-modal').addClass('active');
    });

    // Attach click event to the close button in the modal
    $('.modal-content button.close-btn, .modal-content button:last-child').on('click', function () {
        $('#delete-house-modal').removeClass('active');
    });

    // Attach click event to the confirm button in the modal
    $('.modal-content .buttons button:first-child').on('click', function () {
        var formId = $('#delete-house-modal').data('form-id');
        $('#delete-house-' + formId).submit();
        // Close the modal
        $('#delete-house-modal').removeClass('active');
    });
</script>

@endsection