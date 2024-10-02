<?php
//Configuración del calendario
$configuration = ConfigurationData::getAll();
$calendar_start_hour = (isset($configuration['calendar_start_hour'])) ? $configuration['calendar_start_hour']->value : "7:00:00";
$calendar_end_hour = (isset($configuration['calendar_end_hour'])) ? $configuration['calendar_end_hour']->value : "22:00:00";
$searchMedicId = (isset($_GET["searchMedicId"])) ? $_GET["searchMedicId"] : 0;
//Datos del usuario
$medics = MedicData::getAll();
$user = UserData::getLoggedIn();
$userId = $user->id;
$userType = $user->user_type;

/*Mostrar todo el calendario permite elegir un rango de fechas para mostrar
Por defecto, el calendario se muestra desde el mes anterior*/
$filterCalendar = isset($_GET["filter"])  ? $_GET["filter"] : false;

if ($filterCalendar) {
  //Mostrar el calendario en un rango de fechas
  $startDate = isset($_GET["startDate"])  ? $_GET["startDate"] :  date('Y-m-d');
  $endDate = isset($_GET["endDate"])  ? $_GET["endDate"] :  date('Y-m-d');
  $defaultDate = $startDate;

  $startDateTime = $startDate . " 00:00:01";
  $endDateTime = $endDate . " 23:59:59";

  if ($userType == "do") {
    //Doctores
    $medic = MedicData::getByUserId($userId);
    /*if ($searchMedicId == 0) {
      $searchMedicId = $medic->id;
    }*/
  }

  $events = ReservationData::getBetweenDates($startDateTime, $endDateTime, $searchMedicId);
} else {
  //Mostrar el calendario a partir de una fecha, por defecto
  $defaultDate = date('Y-m-d');

  $startDate = date('Y') . "-" . date("m", strtotime("-1 month")) . "-01"; //Obtener todas las citas desde el mes anterior

  if (date('m') == 01) {
    $startDate = date('Y', strtotime('-1 year')) . "-" . date('m', strtotime('-1 month')) . "-" . date('01');
  } else {
    $startDate = date('Y') . "-" . date('m', strtotime('-1 month')) . "-" . date('01');
  }

  if ($userType == "do") {
    //Doctores
    $medic = MedicData::getByUserId($userId);
    /*if ($searchMedicId == 0) {
      $searchMedicId = $medic->id;
    }*/
  }
  $events = ReservationData::getByStartDate($startDate, $searchMedicId);

  include_once($_SERVER['DOCUMENT_ROOT'] . "/core/app/view/reservations/new-patient-quick.php");
}
?>

<style>
  #delay1 {
    font-size: 24px;
    color: red;
  }

  p {
    color: #000;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        start: 'prev,next today',
        center: 'title',
        end: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      themeSystem: 'standard',
      locale: 'es-us',
      editable: true,
      dayMaxEventRows: true, // allow "more" link when too many events, Era eventLimit en versiones menores a v5
      selectable: true,
      height: 2750, //Original 2750
      expandRows: true,
      navLinks: true,
      initialView: 'timeGridDay',
      slotMinTime: '<?php echo $calendar_start_hour; ?>',
      slotMaxTime: '<?php echo $calendar_end_hour; ?>',
      slotDuration: '00:30:00',
      eventOrderStrict: true,
      eventOrder: 'start,-duration,medicId,allDay',
      eventDisplay: 'block',
      eventDidMount: function(info) {
        // If a description exists add as second line to title
        const a = info.el.getElementsByClassName('fc-event-title');
        a[0].innerHTML = info.event.title;
      },
      select: function(info) {
        var start = (moment(info.start).format('YYYY-MM-DD HH:mm:ss'));
        document.location.href = "./?view=reservations/new-patient&start=" + start + "";
        /*<?php if ($userType != "do") : ?>
          document.location.href = "./?view=reservations/new-patient&start=" + start + "";
        <?php endif; ?>*/
      },
      eventClick: function(info) { //Antes eventRender
        //info.jsEvent.preventDefault(); // don't let the browser navigate
        var id = (info.event.id);
        var reservationType = (info.event.extendedProps.reservationType);
        var url = "";

        if ("<?php echo $userType ?>" == "su") {
          url = "./?view=reservations/details&id=" + id + "";
        } else if ("<?php echo $userType ?>" == "r") {
          url = "./?view=reservations/edit-patient&id=" + id + "";
        } else if ("<?php echo $userType ?>" == "do") {
          url = "./?view=reservations/details&id=" + id + "";
        } else if (reservationType == "medic") {
          url = "./?view=reservations/edit-medic&id=" + id + "";
        } else {
          url = "./?view=reservations/new-patient";
        }
        window.open(url, "_blank");
      },
      events: [
        <?php
        foreach ($events as $event) :
          $reason = addcslashes($event->reason, "\n\r");
          $hour = $event->getStartTime();
          $start = explode(" ", $event->date_at);
          if ($start[1] == '00:00:00') {
            $start = $start[0];
          } else {
            $start = $event->date_at;
          }
          $end = $event->date_at_final;

          //Mostrar el nombre del paciente o del doctor dependiendo del tipo de cita.
          if (isset($event->patient_id) && $event->patient_id != null) {
            $userName = $event->patient_name;
            $reservationType = "patient";
            $phone = (($userType == "do") ? "" : $event->patient_phone);
          } else {
            $userName = $event->medic_name;
            $phone = "";
            $reservationType = "medic";
          }

          $category = $event->reservation_category_name;
          $color = $event->calendar_color;

          $statusName = "";

          if ($event->status_id == 2) {
            $statusName = "<b>ASISTIÓ</b>"; //<i class='fa-solid fa-check'></i>
          } else if ($event->status_id == 3) {
            $statusName = "</i><b>NO ASISTIÓ</b>"; //<i class='fa-solid fa-xmark'>
          } else if ($event->status_id == 4) {
            $statusName = "<b>CANCELADA</b>"; //<i class='fa-solid fa-ban'></i>
          }

          $saleStatus = "<i class='fa-solid fa-triangle-exclamation'></i><i class='fa-solid fa-dollar'></i>VENTA NO GENERADA"; //Venta no generada (Pago no generado)
          if (isset($event->sale_id)) { //Existe una venta
            if ($event->sale_status_id == 0) { //La venta está pendiente
              $saleStatus = "<i class='fa-regular fa-clock'></i><i class='fa-solid fa-dollar-sign'></i>NO LIQUIDADA"; //Venta no liquidada (Pago pendiente liquidar)
            } else { //La venta está liquidada
              $saleStatus = "<i class='fa-solid fa-money-bill-1'></i>LIQUIDADA"; //Venta liquidada (Pago liquidado)
            }
          }

        ?> {
            id: `<?php echo $event->id; ?>`,
            title: `<?php echo $statusName . ' ' . $saleStatus . '<br>' . $userName . '<br>' . $category . '<br>' . $phone . '<br>' . $reason ?>`,
            start: `<?php echo $start; ?>`,
            end: `<?php echo $end; ?>`,
            backgroundColor: `<?php echo $color; ?>`,
            borderColor: `#9FE1E7`,
            textColor: `#000000`,
            extendedProps: { //info.event.extendedProps
              description: `<?php echo $category . '\n' . $phone . '\n' . $reason ?>`,
              medicId: `<?php echo $event->medic_id; ?>`,
              statusId: `<?php echo $event->status_id; ?>`,
              reservationType: `<?php echo $reservationType; ?>`,
              patientId: `<?php echo $event->patient_id; ?>`,
              patientName: `<?php echo $event->patient_name; ?>`,
              medicId: `<?php echo $event->medic_id; ?>`,
              dateAt: `<?php echo $event->date_at; ?>`,
              patientPhone: `<?php echo $event->patient_phone; ?>`
            },
          },

        <?php endforeach; ?>
      ]
    });
    calendar.render();

    $('#selectedDate').on('change', function() {
      var date = $('#selectedDate').val();
      calendar.gotoDate(date);
    });

    function getEventById(id) {
      return calendar.getEventById(id)
    }

  });

  function getCalendar() {
    var date = $('#selectedDate').val();
    $('#calendar').fullCalendar('gotoDate', date);
  }
</script>

<body>
  <!-- /.box-body -->
  <div class="card">
    <div class="card-header" data-background-color="blue">
    </div>
    <div class="card-content table-responsive">
      <div class="row">
        <div class="col-md-6">
          <?php if ($filterCalendar) : ?>
            <div class="callout callout-default">
              <p><i class="fas fa-info-circle"></i> Estás filtrando el calendario por un rango de fechas. Haz clic <a class="bg-primary" href="./index.php?view=home">AQUÍ</a> para regresar al calendario predeterminado.</p>
            </div>
          <?php else : ?>
            <form class="form-horizontal" method="GET" enctype="multipart/form-data" action="index.php" role="form">
              <label class="control-label">Médico</label>
              <div class="input-group">
                <select id="searchMedicId" name="searchMedicId" class="form-control" required>
                  <option value="0" <?php echo ($searchMedicId == 0) ? "selected" : "" ?>>-- TODOS --</option>
                  <?php foreach ($medics as $medic) : ?>
                    <option value="<?php echo $medic->id ?>" <?php echo ($searchMedicId == $medic->id) ? "selected" : "" ?>><?php echo $medic->name ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fas fa-search"></i> Buscar</button>
                  <input type="hidden" name="view" value="home">
                </div>
              </div>
            </form>
          <?php endif; ?>
        </div>
        <div class="col-md-6">
          <div class="row">
            <a href="./index.php?view=reservations/new-patient" class="btn btn-primary btn-xs"><i class='fas fa-user-alt'></i> Nueva cita paciente </a>
            <a href="./index.php?view=reservations/new-medic" class="btn btn-info btn-xs"><i class='fa fa-user-md'></i> Nueva cita doctor</a>
            <?php if ($userType == "su" || $userType == "r") : ?>
              <a href="./index.php?view=sales/new" class="btn btn-success btn-xs"><i class='fa fa-dollar-sign'></i> Realizar venta </a>
            <?php endif; ?>
            <?php if ($filterCalendar) : ?>
              <a href="./index.php?view=home" class="btn btn-default btn-xs"><i class="far fa-calendar-alt"></i> Calendario predeterminado</a>
            <?php else : ?>
              <a href="./index.php?view=home&filter=true" class="btn btn-default btn-xs"><i class="far fa-calendar-alt"></i> Filtrar calendario</a>
            <?php endif; ?>
          </div>
          <div class="row">
            <div class="col-md-6 col-md-offset-6">
              <button class="btn btn-primary btn-sm" onclick="openStartQuickReservation(null)"><i class="fas fa-plus"></i><i class='fas fa-user-alt'></i>Iniciar consulta rápida</button>
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <?php if (!$filterCalendar) : ?>
          <div class="col-lg-6">
            <label class="control-label">Seleccionar fecha a mostrar</label>
            <input id="selectedDate" type="date" class="form-control" min="<?php echo $startDate ?>" value="<?php echo $defaultDate ?>" name="selectedDate" onChange="getCalendar();">
          </div>
        <?php else : ?>
          <form class="form-horizontal" role="form">
            <div class="col-lg-3">
              <label class="control-label">Médico</label>
              <select id="searchMedicId" name="searchMedicId" class="form-control" required>
                <option value="0" <?php echo ($searchMedicId == 0) ? "selected" : "" ?>>-- TODOS --</option>
                <?php foreach ($medics as $medic) : ?>
                  <option value="<?php echo $medic->id ?>" <?php echo ($searchMedicId == $medic->id) ? "selected" : "" ?>><?php echo $medic->name ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-lg-2">
              <label class="control-label">Mostrar desde</label>
              <input id="startDate" type="date" class="form-control" value="<?php echo $startDate ?>" name="startDate">
            </div>
            <div class="col-lg-2">
              <label class="control-label">Mostrar hasta</label>
              <input id="endDate" type="date" class="form-control" value="<?php echo $endDate ?>" name="endDate">
            </div>
            <div class="col-lg-2">
              <br>
              <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-search"></i> Buscar fechas</button>
              <input type="hidden" name="view" value="home">
              <input type="hidden" name="filter" value="true">
            </div>
          </form>
        <?php endif; ?>
        <form class="form-horizontal" role="form">
          <div class="col-md-6">
            <label class="control-label">Buscar citas por médico/paciente/familiar de paciente</label>
            <div class="input-group">
              <input type="text" name="search" id="search" value="" class="form-control" placeholder="Escribe el nombre" autocomplete="off" required>
              <div class="input-group-btn">
                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i> Buscar</button>
                <input type="hidden" name="view" value="reservations/search-history">
              </div>
            </div>
          </div>
        </form>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
          <div class="callout callout-default">
            <p><i class="fas fa-info-circle"></i><b> ÍCONOS DE CITAS:</b> Venta Liquidada (<i class="fa-solid fa-money-bill-1"></i>) Venta Pendiente liquidar(<i class="fa-regular fa-clock"></i><i class="fa-solid fa-dollar-sign"></i>)
              <br>Venta sin generar(<i class="fa-solid fa-triangle-exclamation"></i><i class="fa-solid fa-dollar-sign"></i>)
            </p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
</body>