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
    
</div>
<!-- Include FullCalendar dependencies -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core/main.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid/main.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid/main.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/nl.js"></script>


<script>
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
        slotMinTime: '8:00:00',
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
        editLink = `<i class="fa-regular fa-square-check fa-lg"></i>`;
        @endif

        eventTitle.insertAdjacentHTML('afterend', editLink);
    });
});
</script>
@endsection