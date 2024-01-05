@extends('layout/app')

@section('content')
<div id="add-house">
    <div class="content-header">
        <h1>Vakantiehuis/Hotelkamer toevoegen</h1>
    </div>
    <div class="add-house">
        @if(Session::has('success'))
        <div class="success-message" role="alert">
            {{ Session::get('success') }}
        </div>
        @endif
        <form action="{{ route('createHousePost') }}" method="POST">
            @csrf
            <div class="input">
                <label for="name">Naam</label>
                <input type="text" name="name" id="naam" placeholder="Vul hier het vakantiehuis/hotelkamer in (Hotelkamer 301)" maxlength="255" required>
            </div>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="input">
                <label for="elements">Element <i class="fa-solid fa-plus" id="addElement"></i></label>
                <div id="elementsContainer" class="elements-container">
                    <div class="slideshow-container">
                        <div class="element" style="display: block;">
                            <input type="text" name="element[1][name]" placeholder="Naam(Kleine wc)" maxlength="20">
                            <input type="text" name="element[1][time]" placeholder="Tijd (10 minuten)" maxlength="20">
                            <i class="fa-solid fa-minus removeElement"></i>
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
            
            <button>Account aanmaken</button>
        </form>
    </div>
</div>
<!-- Ensure the script comes after jQuery inclusion -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
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
        $('#addElement').on("click", function () {
            addSlide();
            showElement(++currentElement);
        });

        // Verwijder element
        $('#elementsContainer').on("click", ".removeElement", function () {
            $(this).closest('.element').remove();
            showElement(currentElement);
        });
    });

    let elementCounter = 1;

    function addSlide() {
        elementCounter++;
        let newElement = $('<div class="element" style="display: none;">' +
            '<input type="text" name="element[' + elementCounter + '][name]" placeholder="Naam(Kleine wc)" maxlength="20">' +
            '<input type="text" name="element[' + elementCounter + '][time]" placeholder="Tijd (10 minuten)" maxlength="20">' +
            '<i class="fa-solid fa-minus removeElement"></i>' +
            '</div>');

        $('#elementsContainer .slideshow-container').append(newElement);
    }
    </script>
@endsection
