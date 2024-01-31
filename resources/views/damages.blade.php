@extends('layout/app')

@section('content')
<div id="damages">
    <div class="header">
        <h1>Schades</h1>
    </div>

    @if (count($damages) > 0)
        <div class="damages">
            @foreach ($damages as $damage)
            <div class="damage">
                <div class="info">
                    
                    <p class="name">{{$damage->house->name}}</p>
                    <div class="divider"></div>
                    <p>{{$damage->name}}</p>
                    <div class="divider"></div>
                    @if($damage->status == "1")
                        @if($damage->need == "1")
                            <p>Nood</p>
                        @endif
                    @else
                    <p>Hersteld</p>
                    @endif
                </div>
                @if($damage->status =="1")
                <div class="right">
                    <form id="update-damage-{{$damage->id}}" class="update-damage" method="post" action="{{ route('damages.updateDamage', ['damageId' => $damage->id]) }}" title="Voltooien schade">
                        @csrf
                        @method('post')
                        <button type="button" data-form-id="{{$damage->id}}">
                            <i class="fa-regular fa-square-check"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach    
        </div>
    @else
        <div class="no-damages">
            <p>Er zijn geen schade gevonden</p>
        </div>
    @endif

    <div id="update-damage-modal">
        <div class="modal-content">
            <div class="close-btn">
                <button type="button"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <h2>Is deze schade hersteld?</h2>
            <div class="buttons">
                <button class="button">Afronden</button>
            </div>
        </div>
    </div>

</div>

<script>
// Attach click event to the delete button
$('.update-damage button').on('click', function () {
        var formId = $(this).data('form-id');
        $('#update-damage-modal').data('form-id', formId);
        $('#update-damage-modal').addClass('active');
    });

    // Attach click event to the close button in the modal
    $('.modal-content button.close-btn, .modal-content button:last-child').on('click', function () {
        $('#update-damage-modal').removeClass('active');
    });

    // Attach click event to the confirm button in the modal
    $('.modal-content .buttons button:first-child').on('click', function () {
        var formId = $('#update-damage-modal').data('form-id');
        $('#update-damage-' + formId).submit();
        // Close the modal
        $('#update-damage-modal').removeClass('active');
    });
</script>

@endsection