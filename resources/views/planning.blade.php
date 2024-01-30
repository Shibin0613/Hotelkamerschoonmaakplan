@extends('layout/app')

@section('content')
<div id="planningen">
    <div class="header">
        <h1>Planning</h1>
    </div>
    @if(Auth::user()->role === 1)
    <div class="add-planning">
        <a href="/createPlanning"><i class="fa-solid fa-plus"></i></a>
    </div>
    @endif
    
    <div id="calendar"></div>

    <div id="finish-planning-modal">
        <div class="modal-content">
            <div class="close-btn">
                <button type="button"><i class="fa-solid fa-xmark"></i></button>
            </div>

            @foreach($planningen as $planning)
                <h2>{{$planning->house->name}}</h2>
                <form method="post" action="{{ route('updatedplanning.updatedPlanning', ['planningId' => $planning->id]) }}">
                    @csrf
                    <p>
                        @foreach(json_decode($planning->element) as $elementId => $element)
                            {{$element->name}} {{$element->time}} minuten 
                            <input name="elements[{{$elementId}}][name]" hidden value="{{$element->name}}">
                            <input name="elements[{{$elementId}}][time]" hidden value="{{$element->time}}">
                            @if(!isset($element->onoff))
                                <input type='checkbox' name="elements[{{$elementId}}][onoff]"><br>
                            @else
                                <input type='checkbox' checked name="elements[{{$elementId}}][onoff]" style="pointer-events: none;"/><br>
                            @endif
                        @endforeach
                    </p>
                    <label for="elements">Schade <i class="fa-solid fa-plus" id="addDamage"></i></label>
                    <div id="elementsContainer" class="elements-container">
                        <div class="slideshow-container">
                            <div class="element" style="display: block;">
                                <input type="text" name="damage" placeholder="WC kapot?" maxlength="20">
                                <i class="fa-solid fa-minus removeElement"></i>
                                <p>Nood?</p>
                                <label class="switch">
                                    <input type="checkbox" name="need">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="text-align:center">
                        <div id="dotContainer"></div>
                    </div>

                    <div class="buttons">
                        <button class="button">Versturen</button>
                    </div>
                </form>
            @endforeach
        </div>
    </div>
    
</div>
<!-- Include FullCalendar dependencies -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core/main.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid/main.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid/main.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/nl.js"></script>

<script>
//calendar app
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var planningen = @json($planningen); 
    var userRole = @json(Auth::user()->role);

    var events = planningen.map(function(planning) {
        var cleanerNames = planning.cleaners.map(function(cleaner) {
            return cleaner.firstname + ' ' + cleaner.lastname;
        });

        var eventColor;

        if (planning.status === 1) {
            eventColor = '#e67539';
        } else if (planning.status === 2) {
            eventColor = '#C41E3A';
        } else {
            eventColor = '#50C878';
        }

        return {
            title: planning.house.name + '\u000A' + cleanerNames.join('\u000A'),
            start: planning.startdatetime,
            end: planning.enddatetime,
            planningId: planning.id,
            className: ['eventWithComment'],
            color: eventColor,
        };
    });

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        firstDay: 1,
        slotMinTime: '10:00:00',
        slotMaxTime: '18:00:00',
        events: events,
        locale: 'nl',
        buttonText: {
            today: 'Vandaag',
            thisweek: 'deze week', // Combine buttonText options
        },
        allDaySlot: false,
    });

    calendar.render();

    // Add the edit link after each event
    calendarEl.querySelectorAll('.fc-event-title').forEach(function(eventTitle, index) {
        var planningId = events[index].planningId;
        var editLink = '';
        
        // Check if the user role is 1
        @if(Auth::user()->role === 1)
            editLink = '<a href="{{ url('editPlanning') }}/' + planningId + '" title="wijzig planning"><i class="fa-regular fa-pen-to-square"></i></a>';
        @elseif(Auth::user()->role === 0)
            editLink = `
            <form id="finish-planning-${planningId}" class="finish-planning" method="post" action="/planning/${planningId}" title="Voltooien planning">
                @csrf
                <button type="button" data-form-id="${planningId}">
                    <i class="fa-regular fa-square-check"></i>
                </button>
            </form>`;
        @endif

        eventTitle.insertAdjacentHTML('afterend', editLink);
    });
});

//schade melden+-
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
        $('#addDamage').on("click", function () {
            addSlide();
            showElement(++currentElement);
        });

        // Verwijder element
        $('#elementsContainer').on("click", ".removeElement", function () {
            $(this).closest('.element').remove();
            showElement(currentElement);
        });
    });

let damageCounter = 1;

function addSlide() {
    damageCounter++;
    let newElement = $('<div class="element" style="display: none;">' +
        '<input type="text" name="damage[' + damageCounter + '][name]" placeholder="Keuken kapot?" maxlength="20">' +
        '<i class="fa-solid fa-minus removeElement"></i>' +
        '<p>Nood?</p><label class="switch"><input type="checkbox"><span class="slider round"></span></label>' +
        '</div>');

    $('#elementsContainer .slideshow-container').append(newElement);
}

//finish planning modal button
$(document).on('click', '.finish-planning button', function () {
    var formId = $(this).data('form-id');
    $('#finish-planning-modal').data('form-id', formId);
    $('#finish-planning-modal').addClass('active');
});

$(document).on('click', '.modal-content button.close-btn, .modal-content button:last-child', function () {
    $('#finish-planning-modal').removeClass('active');
});
</script>
@endsection