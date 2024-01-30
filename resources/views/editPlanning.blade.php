@extends('layout/app')

@section('content')
<div id="edit-planning">
    <div class="content-header">
        <h1>Inplannen</h1>
    </div>
    <div class="edit-planning">
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
        @foreach($planning as $planning)
        <form action="{{ route('updatePlanning', ['planningId' => $planning->id]) }}" method="POST">
            @csrf
            <div class="input" id="houseSelect">
                <label for="house">Vakantiehuis/Hotelkamer</label>
                <select name="house" id="house">
                    <option value="{{ $planning->house->id }}" hidden selected disabled>{{$planning->house->name}}</option>
                    @foreach ($houses as $house)  
                        <option value="{{ $house->id }}">{{ $house->name }}</option>
                    @endforeach
                </select>
            </div>
            @error('house')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input" id="element-input">
                <label for="element">Elements</label>
                <div id="element-checkboxes">
                    @foreach(json_decode($planning->element) as $key =>$element)
                    <label>{{$element->name}} {{$element->time}} minuten 
                        <input type="checkbox" checked name="selected_elements[{{$key}}][name]" value="{{$element->name}}">
                        <input type="hidden" name="selected_elements[{{$key}}][time]" value="{{$element->time}}" id="time_{{$key}}">
                    </label>
                    @endforeach
                    <!-- Checkboxes will be dynamically added here based on the selected house -->
                </div>
            </div>
            
            <div class="input" id="houseSelect">
                <label for="house">Startdatum tijd</label>
                    <input type="datetime-local" name="startdatetime" value="{{$planning->startdatetime}}">
            </div>
            @error('startdatetime')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input" id="houseSelect">
                <label for="house">Einddatum tijd</label>
                    <input type="datetime-local" name="enddatetime" value="{{$planning->enddatetime}}">
            </div>
            @error('enddatetime')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input">
                <label for="schoonmakers">Aantal beschikbare schoonmakers</label>
                <div class = "dropdown" onclick = "showOptions()">
                    Selecteer aantal schoonmakers
                </div>
                <div id = "options">
                    @foreach($planning->cleaners as $cleaner)
                    <label>
                    {{$cleaner->firstname}}<input id="input-row" type="checkbox" checked name="schoonmakers[]" value="{{$cleaner->id}}">
                    </label>
                    @endforeach
                    @foreach ($cleanersNotInPlanning as $cleanerNotInPlanning)
                        <label>
                            {{$cleanerNotInPlanning->firstname}}<input id="input-row" type="checkbox" name="schoonmakers[]" value="{{$cleanerNotInPlanning->id}}">
                        </label>
                    @endforeach
                </div>
            </div>
            @error('schoonmakers')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input">
                <label for="decorations">Extra decoratie <i class="fa-solid fa-plus" id="addDecoration"></i></label>

                <div id="elementsContainer" class="elements-container">
                    <div class="slideshow-container">
                        <div id="elementsContainer">
                            @foreach($planning->decorations as $key => $decoration)
                            <div class="element" style="display: block;">
                                <input type="text" name="decoration[{{$key}}][name]" placeholder="Naam(Pasendecoratie)" maxlength="20" value="{{$decoration->name}}">
                                <input type="int" name="decoration[{{$key}}][time]" placeholder="Tijd (10 minuten)" maxlength="20" value="{{$decoration->time}}">
                                <i class="fa-solid fa-minus removeElement"></i>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
            </div>
            <div style="text-align:center">
                <div id="dotContainer"></div>
            </div>
            
            <button>Update Planning</button>
        </form>
        @endforeach
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    document.querySelectorAll('input[type=checkbox]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var key = this.name.match(/\d+/)[0]; // Haal het nummer uit de checkbox-naam
            var timeInput = document.getElementById('time_' + key);

            // Schakel de tijdinput uit als de checkbox is uitgeschakeld
            if (!this.checked) {
                timeInput.disabled = true;
            } else {
                timeInput.disabled = false;
            }
        });
    });
    let output = document.getElementById('output');
      var showCheckBoxes = true;

      function showOptions() {
         var options =
            document.getElementById("options");

         if (showCheckBoxes) {
            options.style.display = "flex";
            showCheckBoxes = !showCheckBoxes;
         } else {
            options.style.display = "none";
            showCheckBoxes = !showCheckBoxes;
         }
      }
      function getOptions() {
         var selectedOptions = document.querySelectorAll('input[type=checkbox]:checked')
         output.innerHTML = "The selected options are given below. <br/>";
         for (var i = 0; i < selectedOptions.length; i++) {
            output.innerHTML += selectedOptions[i].value + " , ";
            console.log(selectedOptions[i])
         }
      }
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

            let elementCounter = 0;
            // Check if it's an array before creating checkboxes
            if (Array.isArray(selectedHouseElements)) {
                selectedHouseElements.forEach(function (element) {

                    elementCounter++;

                    const checkbox = $(`<label>${element.name} ${element.time} minuten` + `&nbsp&nbsp`+ 
                    `<input type="checkbox" checked ` + 'name="selected_elements[' + elementCounter + '][name]"' + `value="${element.name}">` + 
                    `<input hidden ` + 'name="selected_elements[' + elementCounter + '][time]"' + ` value="${element.time}"></label>`);
                    
                    elementCheckboxes.append(checkbox);
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
let currentElement = 1;

    // Initialize dot container
    updateDotIndicators(currentElement, 1);

    function navigateElements(n) {
        showElement(currentElement += n);
    }

    function showElement(n) {
        let elements = document.getElementsByClassName("element");
        if (n > elements.length) {
            currentElement = elements.length;
        }
        if (n < 1) { currentElement = 1; }
        for (let i = 0; i < elements.length; i++) {
            elements[i].style.display = "none";
        }
        elements[currentElement - 1].style.display = "block";
        updateDotIndicators(currentElement, elements.length);

        // Toon of verberg navigatieknoppen op basis van de huidige slide
        let prevBtn = document.getElementById("prevBtn");
        let nextBtn = document.getElementById("nextBtn");
        if (currentElement === 1) {
            prevBtn.style.display = "none";
        } else {
            prevBtn.style.display = "block";
        }
        if (currentElement === elements.length) {
            nextBtn.style.display = "none";
        } else {
            nextBtn.style.display = "block";
        }
    }

    function updateDotIndicators(current, total) {
        let dotContainer = document.getElementById("dotContainer");
        if (dotContainer) {
            dotContainer.innerHTML = ""; // Clear existing dots

            for (let i = 1; i <= total; i++) {
                let dot = document.createElement("span");
                dot.className = "dot";
                dot.onclick = function () { currentSlide(i); };
                dotContainer.appendChild(dot);

                if (i === current) {
                    dot.className += " active";
                }
            }
        }
    }

    $(document).ready(function () {
        // Voeg element toe
        $('#addDecoration').on("click", function () {
            addSlide();
            currentElement = $('.element').length; // Update currentElement naar het nieuw toegevoegde element
            showElement(currentElement);
        });

        // Verwijder element
        $('#elementsContainer').on("click", ".removeElement", function () {
            $(this).closest('.element').remove();
            showElement(currentElement);
        });
    });

    let decorationCounter = 1;
    
    function addSlide() {
        decorationCounter++;
        let newElement = $('<div class="element" style="display: none;">' +
            '<input type="text" name="decoration[' + decorationCounter + '][name]" placeholder="Naam(Verjaardagsdecoratie)" maxlength="20">' +
            '<input type="int" name="decoration[' + decorationCounter + '][time]" placeholder="Tijd (15 minuten)" maxlength="20">' +
            '<i class="fa-solid fa-minus removeElement"></i>' +
            '</div>');

        $('#elementsContainer .slideshow-container').append(newElement);
    }
</script>

@endsection