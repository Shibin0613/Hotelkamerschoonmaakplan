@extends('layout/app')

@section('content')
<div id="edit-house">
    <div class="content-header">
        <h1>Vakantiehuis/Hotelkamer aanpassen</h1>
    </div>
    <div class="edit-house">
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
        @foreach($house as $house)
        <form action="{{ route('updateHouse', ['houseId' => $house->id]) }}" method="POST">
            @csrf
            <div class="input">
                <label for="name">Naam</label>
                <input type="text" name="name" id="naam" placeholder="{{$house->name}}" maxlength="255">
            </div>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input">
                <label for="elements">Element <i class="fa-solid fa-plus" id="addElement"></i></label>
                
                <div id="elementsContainer" class="elements-container">
                    <div class="slideshow-container">
                        <div id="elementsContainer">
                            @if(!empty($house->elements))
                                @foreach(json_decode($house->elements) as $key => $element)
                                <div class="element" style="display: block;">
                                    <input type="text" name="element[{{$key}}][name]" value="{{$element->name}}" maxlength="20">
                                    <input type="int" name="element[{{$key}}][time]" value="{{$element->time}}" maxlength="20">
                                    <i class="fa-solid fa-minus removeElement"></i>
                                </div>
                                @endforeach
                            @elseif(empty($house->elements))
                            <input type="text" name="element[1][name]" placeholder="Naam(Kleine Wc)" maxlength="20">
                            <input type="int" name="element[1][time]" placeholder="TIjd (10 minuten)" maxlength="20">
                            @endif
                        </div>
                    </div>
                </div>

                <a class="prev" id="prevBtn" style="display: none;" onclick="navigateElements(-1)">❮</a>
                <a class="next" id="nextBtn" style="display: none;" onclick="navigateElements(1)">❯</a>
            </div>
            <div style="text-align:center">
                <div id="dotContainer"></div>
            </div>
            @error('elements')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            @endforeach
            
            <button>Huis opslaan</button>
        </form>
    </div>
</div>
<!-- Ensure the script comes after jQuery inclusion -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
let currentElement = 1;

// Initialize dot container
updateDotIndicators(currentElement, $('.element').length);

function navigateElements(n) {
    showElement(currentElement + n);
}

function showElement(n) {
    let elements = document.getElementsByClassName("element");  
    if (n > elements.length) {
        currentElement = 1;
    } else if (n < 1) {
        currentElement = elements.length;
    } else {
        currentElement = n;
    }

    for (let i = 0; i < elements.length; i++) {
        elements[i].style.display = "none";
    }
    elements[currentElement - 1].style.display = "block";
    updateDotIndicators(currentElement, elements.length);

    // Bij het verwijderen van een element, werk de elementCounter en de nummering van de elementen bij
    elementCounter = elements.length;
    updateElementNumbering();
}

function updateElementNumbering() {
    $('.element').each(function (index) {
        let newNumber = index + 1;
        $(this).find('input[name^="element"]').each(function () {
            let newName = $(this).attr('name').replace(/\[\d+\]/, '[' + newNumber + ']');
            $(this).attr('name', newName);
        });
    });
}

function updateDotIndicators(current, total) {
    let dotContainer = document.getElementById("dotContainer");
    if (dotContainer) {
        dotContainer.innerHTML = "";

        for (let i = 1; i <= total; i++) {
            let dot = document.createElement("span");
            dot.className = "dot";
            dot.onclick = function () {
                showElement(i);
            };
            dotContainer.appendChild(dot);

            if (i === current) {
                dot.className += " active";
            }
        }
    }
}

$(document).ready(function () {
    // Voeg element toe
    $('#addElement').on("click", function () {
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

let elementCounter = {{ count(json_decode($house->elements, true))}};

function addSlide() {
    elementCounter++;
    let newElement = $('<div class="element" style="display: none;">' +
        '<input type="text" name="element[' + elementCounter + '][name]" placeholder="Naam(Keuken)" maxlength="20">' +
        '<input type="text" name="element[' + elementCounter + '][time]" placeholder="Tijd (20 minuten)" maxlength="20">' +
        '<i class="fa-solid fa-minus removeElement"></i>' +
        '</div>');

    $('#elementsContainer .slideshow-container').append(newElement);
}
</script>
@endsection
