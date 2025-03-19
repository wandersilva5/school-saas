<!-- Content Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= $pageTitle ?></h1>
</div>
<div class="row">
    <div class="col-xl-8 col-md-12 mb-4">
        <div class="content-section">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="section-title"><i class="bi bi-calendar"></i></h5>
                <?php if (current_user_can('Secretaria')): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal">
                        <i class="bi bi-plus"></i> Novo Evento
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Eventos do dia -->
    <div class="col-xl-4 col-md-12 mb-4">
        <div class="content-section">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="section-title">Eventos do dia <span id="selectedDate"></span></h5>
            </div>
            <div class="card-body">
                <div class="card-body">
                    <div id="dayEvents"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Evento -->
    <div class="modal fade" id="eventModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Novo Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="eventForm" action="/calendar/store" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Data</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Hora Início</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">Hora Fim</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php push('scripts') ?>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa o modal usando Bootstrap 5.3.0
            const eventModal = new bootstrap.Modal(document.getElementById('eventModal'), {
                backdrop: 'static'
            });

            // Inicializa o FullCalendar
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                locale: 'pt-br',
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    day: 'Dia'
                },
                events: <?= json_encode(array_map(function ($event) {
                            return [
                                'id' => $event['id'],
                                'title' => $event['title'],
                                'start' => $event['date'] . 'T' . $event['start_time'],
                                'end' => $event['date'] . 'T' . $event['end_time'],
                                'description' => $event['description']
                            ];
                        }, $events)) ?>,
                dateClick: function(info) {
                    const formattedDate = info.dateStr;
                    document.getElementById('date').value = formattedDate;
                    loadDayEvents(formattedDate);
                },
                eventClick: function(info) {
                    document.getElementById('title').value = info.event.title;
                    document.getElementById('description').value = info.event.extendedProps.description || '';
                    document.getElementById('date').value = info.event.startStr.split('T')[0];
                    document.getElementById('start_time').value = info.event.startStr.split('T')[1]?.substring(0, 5) || '';
                    document.getElementById('end_time').value = info.event.endStr?.split('T')[1]?.substring(0, 5) || '';
                    eventModal.show();
                },
                eventDidMount: function(info) {
                    // Adiciona tooltips do Bootstrap
                    new bootstrap.Tooltip(info.el, {
                        title: info.event.title,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                }
            });
            calendar.render();

            // Carregar eventos do dia atual ao iniciar
            const today = <?= json_encode($today) ?>;
            const todayEvents = <?= json_encode($todayEvents) ?>;
            displayDayEvents(todayEvents, today);
        });

        function loadDayEvents(date) {
            document.getElementById('dayEvents').innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>';
            
            // Ensure we have a proper date format YYYY-MM-DD
            const formattedDate = date.split('T')[0];
            
            // Make the fetch request
            fetch(`/calendar/day-events/${formattedDate}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    displayDayEvents(data.events, data.date);
                } else {
                    throw new Error(data.error || 'Erro ao carregar eventos');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('dayEvents').innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x fs-2 d-block mb-3"></i>
                        <p class="mb-1">Não existe evento para o dia selecionado,</p>
                        <p>consulte a instituição.</p>
                    </div>`;
            });
        }

        function displayDayEvents(events, date) {
            const container = document.getElementById('dayEvents');
            const dateDisplay = document.getElementById('selectedDate');

            // Ensure date is in the correct format
            const eventDate = new Date(date);
            const formattedDate = eventDate.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });

            dateDisplay.textContent = ` - ${formattedDate}`;

            if (!events || events.length === 0) {
                container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-calendar-x fs-2 d-block mb-3"></i>
                <p class="mb-1">Não existe evento para o dia selecionado,</p>
                <p>consulte a instituição.</p>
            </div>`;
                return;
            }

            let html = '<div class="list-group">';
            events.forEach(event => {
                html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-1">${event.title}</h6>
                    <small class="text-muted">${event.start_time} - ${event.end_time}</small>
                </div>
                ${event.description ? `<p class="mb-1 text-muted small">${event.description}</p>` : ''}
            </div>
        `;
            });
            html += '</div>';
            container.innerHTML = html;
        }
    </script>
    <?php endpush() ?>