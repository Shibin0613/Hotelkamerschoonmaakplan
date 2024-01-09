@extends('layout/app')

@section('content')
<div id="add-planning">
    <div class="content-header">
        <h1>Inplannen</h1>
    </div>
    <div class="add-planning">
        @if(Session::has('success'))
        <div class="success-message" role="alert">
            {{ Session::get('success') }}
        </div>
        @endif
        <form action="{{ route('createPlanningPost') }}" method="POST">
            @csrf
            <div class="input" id="houseSelect">
                <label for="house">Vakantiehuis/Hotelkamer</label>
                <select name="house" id="house" required>
                    <option hidden selected disabled>Selecteer een vakantiehuis/hotelkamer</option>
                    @foreach ($houses as $house)
                        <option value="{{ $house->id }}">{{ $house->name }}</option>
                    @endforeach
                </select>
            </div>
            @error('house')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input d-none" id="element-input">
                <label for="element">Elements</label>
                <div id="element-checkboxes">
                    <!-- Checkboxes will be dynamically added here based on the selected house -->
                </div>
            </div>
            <div class="input" id="houseSelect">
                <label for="house">Datum en tijd</label>
                    <input type="datetime-local">
            </div>
            @error('datum')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            
            <button>Plan in</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function () {
    const houseSelect = $('#house');
    const elementInput = $('#element-input');
    const elementCheckboxes = $('#element-checkboxes');

    houseSelect.on('change', function () {
        const selectedHouseId = $(this).val();
        const elements = @json($elements);

        // Clear existing checkboxes
        elementCheckboxes.empty();

        // Populate checkboxes based on the selected house
        if (elements[selectedHouseId]) {
            const selectedHouseElements = Object.values(elements[selectedHouseId]);

            // Check if it's an array before creating checkboxes
            if (Array.isArray(selectedHouseElements)) {
                selectedHouseElements.forEach(function (element) {
                    const checkbox = $(`<label>${element.name} ${element.time} minuten <input type="checkbox" name="selected_elements[]" value="${element.name}"></label>`);
                    
                    elementCheckboxes.append('&nbsp').append(checkbox).append('&nbsp');
                });
                elementInput.removeClass('d-none');
            } else {
                console.error('Selected house elements is not an array:', selectedHouseElements);
                elementInput.addClass('d-none');
            }
        } else {
            elementInput.addClass('d-none');
        }
    });
});
</script>

@endsection