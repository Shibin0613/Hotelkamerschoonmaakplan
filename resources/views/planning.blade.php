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


<!-- Render the calendar using Blade syntax -->
<div id="calendar"></div>

<script> 
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        slotMinTime: '8:00:00',
        slotMaxTime: '18:00:00',
        events: @json($events),

        eventRender: function (info) {
            var button = document.createElement('button');
            button.innerHTML = 'Mijn Knop';
            button.addEventListener('click', function () {
                // Voeg hier de logica toe die je wilt uitvoeren wanneer de knop wordt geklikt
                console.log('Knop geklikt voor evenement:', info.event);
            });

            // Voeg de knop toe onder het evenement
            info.el.appendChild(button);
        },
    });
    calendar.render();
});
</script>
@endsection