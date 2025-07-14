     <div class="col-md-6">
         <div class="card mt-3">
            <div class="card-header pb-0">
                <h5 class="card-title text-capitalize">Kalender Akademik</h5>
            </div>
             <div class="card-body">
                 <div id="calender"></div>
             </div>
         </div>
     </div>
     <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.17/index.global.min.js'></script>
     <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.17/index.global.min.js'></script>
     <script>
         document.addEventListener('DOMContentLoaded', function() {
             const calendarEl = document.getElementById('calender');
             const calendar = new FullCalendar.Calendar(calendarEl, {
                 initialView: 'dayGridMonth',
                 events: @json($kalenderAkademik),
                 eventDidMount: function(info) {
                     info.el.setAttribute("title", info.event.title);
                 }
             });
             calendar.render();
         });
     </script>
