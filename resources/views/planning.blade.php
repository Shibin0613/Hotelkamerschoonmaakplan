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

    <p>
    @foreach($planningen as $planning)
    {{$planning->house->name}}
    Datum: {{$planning->datetime}}
    Element: 
    @foreach(json_decode($planning->element) as $element)
    {{$element->name}} {{$element->time}} minuten<input type="checkbox">
    @endforeach
    @if(Auth::user()->role === 1)
    <a href="{{ route('editPlanning', ['planningId' => $planning->id]) }}" title="wijzig planning"><i class="fa-regular fa-pen-to-square"></i></a> <i class="fa-regular fa-square-plus"></i>
    @elseif(Auth::user()->role === 0)
    <i class="fa-regular fa-square-check"></i>
    @endif
    <label>
      Nood?
      <input type="checkbox">
      Status:
      @if($planning->status === 0)
        oranje
      @elseif($planning->status === 1)
        @if(!isset($planning->damage))
        groen
        @else
          rood
        @endif
      @endif
      
      Schoonmakers:
      @foreach($planning->cleaners as $cleaner)
      {{$cleaner->firstname}} {{$cleaner->lastname}}
      @endforeach
      @endforeach
    </label>
    </p>
    <div id="calendar"></div>
    
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.1/moment-with-locales.min.js"></script>
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
        // Voeg extra decoratie toe
        $('#addDecoration').on("click", function () {
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
            '<input type="text" name="damge[' + damageCounter + '][name]" placeholder="Wat gebeurt er?" maxlength="0">' +
            '<i class="fa-solid fa-minus removeElement"></i>' +
            '</div>');

        $('#elementsContainer .slideshow-container').append(newElement);
    }

//agenda(moment.js)
    !function() {

var today = moment();

function Calendar(selector, events) {
  this.el = document.querySelector(selector);
  this.events = events;
  this.current = moment().locale('nl').date(1);
  this.draw();
  var current = document.querySelector('.today');
  if(current) {
    var self = this;
    window.setTimeout(function() {
      self.openDay(current);
    }, 500);
  }
}

Calendar.prototype.draw = function() {
  //Create Header
  this.drawHeader();

  //Draw Month
  this.drawMonth();

  this.drawLegend();
}

Calendar.prototype.drawHeader = function() {
  var self = this;
  if(!this.header) {
    //Create the header elements
    this.header = createElement('div', 'header');
    this.header.className = 'header';

    this.title = createElement('h1');

    var right = createElement('div', 'right');
    right.addEventListener('click', function() { self.nextMonth(); });

    var left = createElement('div', 'left');
    left.addEventListener('click', function() { self.prevMonth(); });

    //Append the Elements
    this.header.appendChild(this.title); 
    this.header.appendChild(right);
    this.header.appendChild(left);
    this.el.appendChild(this.header);
  }

  this.title.innerHTML = this.current.format('MMMM-YYYY');
}

Calendar.prototype.drawMonth = function() {
  var self = this;
  
  this.events.forEach(function(ev) {
    // Set the event date based on the actual date from the database
    ev.date = moment(ev.date);
  });
  
  if(this.month) {
    this.oldMonth = this.month;
    this.oldMonth.className = 'month out ' + (self.next ? 'next' : 'prev');
    this.oldMonth.addEventListener('webkitAnimationEnd', function() {
      self.oldMonth.parentNode.removeChild(self.oldMonth);
      self.month = createElement('div', 'month');
      self.backFill();
      self.currentMonth();
      self.fowardFill();
      self.el.appendChild(self.month);
      window.setTimeout(function() {
        self.month.className = 'month in ' + (self.next ? 'next' : 'prev');
      }, 16);
    });
  } else {
      this.month = createElement('div', 'month');
      this.el.appendChild(this.month);
      this.backFill();
      this.currentMonth();
      this.fowardFill();
      this.month.className = 'month new';
  }
}

Calendar.prototype.backFill = function() {
  var clone = this.current.clone();
  var dayOfWeek = clone.day();

  if(!dayOfWeek) { return; }

  clone.subtract('days', dayOfWeek+1);

  for(var i = dayOfWeek; i > 0 ; i--) {
    this.drawDay(clone.add('days', 1));
  }
}

Calendar.prototype.fowardFill = function() {
  var clone = this.current.clone().add('months', 1).subtract('days', 1);
  var dayOfWeek = clone.day();

  if(dayOfWeek === 6) { return; }

  for(var i = dayOfWeek; i < 6 ; i++) {
    this.drawDay(clone.add('days', 1));
  }
}

Calendar.prototype.currentMonth = function() {
  var clone = this.current.clone();

  while(clone.month() === this.current.month()) {
    this.drawDay(clone);
    clone.add('days', 1);
  }
}

Calendar.prototype.getWeek = function(day) {
  if(!this.week || day.day() === 0) {
    this.week = createElement('div', 'week');
    this.month.appendChild(this.week);
  }
}

Calendar.prototype.drawDay = function(day) {
  var self = this;
  this.getWeek(day);

  //Outer Day
  var outer = createElement('div', this.getDayClass(day));
  outer.addEventListener('click', function() {
    self.openDay(this);
  });

  //Day Name
  var name = createElement('div', 'day-name', day.format('ddd'));

  //Day Number
  var number = createElement('div', 'day-number', day.format('DD'));


  //Events
  var events = createElement('div', 'day-events');
  this.drawEvents(day, events);

  outer.appendChild(name);
  outer.appendChild(number);
  outer.appendChild(events);
  this.week.appendChild(outer);
}

Calendar.prototype.drawEvents = function(day, element) {
  if(day.month() === this.current.month()) {
    var todaysEvents = this.events.reduce(function(memo, ev) {
      if(ev.date.isSame(day, 'day')) {
        memo.push(ev);
      }
      return memo;
    }, []);

    todaysEvents.forEach(function(ev) {
      var evSpan = createElement('span', ev.color);
      element.appendChild(evSpan);
    });
  }
}

Calendar.prototype.getDayClass = function(day) {
  classes = ['day'];
  if(day.month() !== this.current.month()) {
    classes.push('other');
  } else if (today.isSame(day, 'day')) {
    classes.push('today');
  }
  return classes.join(' ');
}

Calendar.prototype.openDay = function(el) {
  var details, arrow;
  var dayNumber = +el.querySelectorAll('.day-number')[0].innerText || +el.querySelectorAll('.day-number')[0].textContent;
  var day = this.current.clone().date(dayNumber);

  var currentOpened = document.querySelector('.details');

  //Check to see if there is an open detais box on the current row
  if(currentOpened && currentOpened.parentNode === el.parentNode) {
    details = currentOpened;
    arrow = document.querySelector('.arrow');
  } else {
    //Close the open events on differnt week row
    //currentOpened && currentOpened.parentNode.removeChild(currentOpened);
    if(currentOpened) {
      currentOpened.addEventListener('webkitAnimationEnd', function() {
        currentOpened.parentNode.removeChild(currentOpened);
      });
      currentOpened.addEventListener('oanimationend', function() {
        currentOpened.parentNode.removeChild(currentOpened);
      });
      currentOpened.addEventListener('msAnimationEnd', function() {
        currentOpened.parentNode.removeChild(currentOpened);
      });
      currentOpened.addEventListener('animationend', function() {
        currentOpened.parentNode.removeChild(currentOpened);
      });
      currentOpened.className = 'details out';
    }

    //Create the Details Container
    details = createElement('div', 'details in');

    //Create the arrow
    var arrow = createElement('div', 'arrow');

    //Create the event wrapper

    details.appendChild(arrow);
    el.parentNode.appendChild(details);
  }

  var todaysEvents = this.events.reduce(function(memo, ev) {
    if(ev.date.isSame(day, 'day')) {
      memo.push(ev);
    }
    return memo;
  }, []);

  this.renderEvents(todaysEvents, details);

  arrow.style.left = el.offsetLeft - el.parentNode.offsetLeft + 27 + 'px';
}

Calendar.prototype.renderEvents = function(events, ele) {
  //Remove any events in the current details element
  var currentWrapper = ele.querySelector('.events');
  var wrapper = createElement('div', 'events in' + (currentWrapper ? ' new' : ''));

  events.forEach(function(ev) {
    var div = createElement('div', 'event');
    var square = createElement('div', 'event-category ' + ev.color);
    var span = createElement('span', '', ev.eventName);

    div.appendChild(square);
    div.appendChild(span);
    wrapper.appendChild(div);
  });

  if(!events.length) {
    var div = createElement('div', 'event empty');
    var span = createElement('span', '', 'Geen planning vandaag');

    div.appendChild(span);
    wrapper.appendChild(div);
  }

  if(currentWrapper) {
    currentWrapper.className = 'events out';
    currentWrapper.addEventListener('webkitAnimationEnd', function() {
      currentWrapper.parentNode.removeChild(currentWrapper);
      ele.appendChild(wrapper);
    });
    currentWrapper.addEventListener('oanimationend', function() {
      currentWrapper.parentNode.removeChild(currentWrapper);
      ele.appendChild(wrapper);
    });
    currentWrapper.addEventListener('msAnimationEnd', function() {
      currentWrapper.parentNode.removeChild(currentWrapper);
      ele.appendChild(wrapper);
    });
    currentWrapper.addEventListener('animationend', function() {
      currentWrapper.parentNode.removeChild(currentWrapper);
      ele.appendChild(wrapper);
    });
  } else {
    ele.appendChild(wrapper);
  }
}

Calendar.prototype.drawLegend = function() {
  var legend = createElement('div', 'legend');
  var calendars = this.events.map(function(e) {
    return e.calendar + '|' + e.color;
  }).reduce(function(memo, e) {
    if(memo.indexOf(e) === -1) {
      memo.push(e);
    }
    return memo;
  }, []).forEach(function(e) {
    var parts = e.split('|');
    
  });
  
}

Calendar.prototype.nextMonth = function() {
  this.current.add('months', 1);
  this.next = true;
  this.draw();
}

Calendar.prototype.prevMonth = function() {
  this.current.subtract('months', 1);
  this.next = false;
  this.draw();
}

window.Calendar = Calendar;

function createElement(tagName, className, innerText) {
  var ele = document.createElement(tagName);
  if(className) {
    ele.className = className;
  }
  if(innerText) {
    ele.textContent = innerText;
  }
  return ele;
}
}();

!function() {
  var planningData = @json($planningen);
  var linkElement, iconElement;
var data = planningData.map(function(planning) {
  
  var time = moment(planning.datetime).format('HH:mm');
    var cleanersNames = planning.cleaners.map(function(cleaner) {
        return cleaner.firstname;
    }).join(', ');
    
    var editUrl = "{{ route('editPlanning', ['planningId' => $planning->id]) }}";

    // Maak de HTML-elementen aan met behulp van DOM-methoden
    linkElement = document.createElement('a');
    linkElement.href = editUrl;
    linkElement.title = "Wijzig planning";
    linkElement.innerHTML = '<i class="fa-regular fa-pen-to-square"></i>';

    iconElement = document.createElement('i');
    iconElement.className = 'fa-regular fa-square-plus';

    var eventNameHTML = planning.house.name + ' ' + time + ' ' + cleanersNames + ' ' + linkElement.outerHTML + ' ' + iconElement.outerHTML;

    return {
        eventName: eventNameHTML, // Gebruik een geschikt veld uit je planningdata
        calendar: planning.house.name, // Je kunt hier een statische waarde gebruiken of een veld uit je planningdata
        color: getColorBasedOnStatus(planning.status), // Implementeer deze functie om kleuren te bepalen op basis van de status
        date: moment(planning.datetime).format('YYYY-MM-DD HH:mm'),
    };
});
function getColorBasedOnStatus(status) {
  switch (status) {
        case 0:
            return 'green';
        case 1:
            return 'orange';
        case 2:
            return 'red';
    }
}
var linkContainer = document.createElement('div');
linkContainer.appendChild(linkElement);
linkContainer.appendChild(iconElement);

// Voeg de container nu toe aan de kalendercontainer
var calendarContainer = document.getElementById('calendar');
calendarContainer.appendChild(linkContainer);
var calendar = new Calendar('#calendar', data);

}();

</script>
@endsection