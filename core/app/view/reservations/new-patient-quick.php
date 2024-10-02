<?php
date_default_timezone_set('America/Mexico_City');
$patients = PatientData::getAll();
$user = UserData::getLoggedIn();
$userType = $user->user_type;

if ($userType == "do") {
  $medicData = MedicData::getByUserId($_SESSION["user_id"]);
  $defaultMedic =  $medicData->id;
} else {
  $defaultMedic =  0;
}
$medics = MedicData::getAll();
//$medics = [MedicData::getByUserId($_SESSION["user_id"])];

$categories = ReservationData::getReservationCategories();

$laboratories = LaboratoryData::getByStatus(1);
$areas = ReservationAreaData::getAll();
?>
<style>
  .select2-container {
    z-index: 99999 !important;
    width:100% !important;
  }
</style>
<!--- MODAL PATIENT RESERVATION QUICK START -->
<div class="modal fade" aria-hidden="true" role="dialog" id="modalQuickReservation" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Nueva consulta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4" id="quickReservationDiv">
            <label class="col-md-4 control-label">Paciente</label>
            <select id="patientQuickReservation" class="form-control" autofocus required>
              <option value="" disabled selected>-- SELECCIONE -- </option>
              <?php foreach ($patients as $patient) : ?>
                <option value="<?php echo $patient->id; ?>"><?php echo $patient->id . " - " . $patient->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <label class="col-md-3 control-label">Consultorio/Laboratorio</label>
            <select id="laboratoryQuickReservation" class="form-control" required>
            </select>
          </div>
          <div class="col-md-4">
            <label class="col-md-3 control-label">Categoría</label>
            <select id="categoryQuickReservation" class="form-control" required>
              <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category->id; ?>"><?php echo $category->id . " - " . $category->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="col-md-3 control-label">Área</label>
            <select id="areaQuickReservation" class="form-control" required>
              <?php foreach ($areas as $area) : ?>
                <option value="<?php echo $area->id; ?>"><?php echo $area->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12" id="divAlertActualReservations">
            <div class="alert alert-danger"><h5><i class="icon fas fa-ban"></i> Advertencia</h5>Tienes otras consultas en curso, finalízalas.</div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12" id="divActualReservations">

          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSaveQuickReservation" onclick="addQuickReservation()">Iniciar consulta</button>
      </div>
    </div>
  </div>
</div>
<!--- MODAL PATIENT RESERVATION QUICK START -->

<script type="text/javascript">
  var Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000
  });

  $(document).ready(function() {
    $("#laboratoryQuickReservation").select2({});
    $("#patientQuickReservation").select2({});
    $("#categoryQuickReservation").select2({});
    $("#areaQuickReservation").select2({});
  });

  function validateLaboratoriesQuickReservation() {
    $.ajax({
      url: "./?action=laboratories/validate-availability", // json datasource
      type: "POST", // method, by default get
      data: {
        date: getDateQuickReservation(),
        timeAt: getStartTimeQuickReservation(),
        timeAtFinal: getEndTimeQuickReservation(),
        reservationId: 0
      },
      success: function(data) {
        $('#laboratoryQuickReservation').select2('destroy');
        $('#laboratoryQuickReservation').empty();
        let laboratories = JSON.parse(data);
        let html = '';
        $("#laboratoryQuickReservation").append(html);

        $.each(laboratories, function(index, laboratory) {
          let html = '<option value="' + laboratory.id + '">' + laboratory.name + '</option>';
          $("#laboratoryQuickReservation").append(html);
        });
        $('#laboratoryQuickReservation').select2();

      },
      error: function() { // error handling
        Toast.fire({
          icon: 'error',
          title: 'Error al mostrar los consultorios disponibles, recarga la página.'
        });
      }
    });
  }

  function validateOtherReservationsQuickReservation() {

    //Validar si ya existen otras citas del médico que no se han finalizado
    $.ajax({
      url: "./?action=reservations/validate-active-reservations", // json datasource
      type: "POST", // method, by default get
      data: {
        date: getDateQuickReservation(),
        timeAt: getStartTimeQuickReservation(),
        timeAtFinal: getEndTimeQuickReservation(),
        reservationId: 0
      },
      success: function(data) {
        $('#divActualReservations').empty();
        if (data) {
          $("#btnSaveQuickReservation").prop('disabled', true);
          let reservations = JSON.parse(data);
          if(Object.keys(reservations).length){
            let html = "";

            $.each(reservations, function(index, reservation) {
              html += '<div class="row" id="divOtherRQuickReservation' + reservation.id + '">';
              html += '<div class="col-md-8">' + reservation.patient_name + '</div>';
              html += '<div class="col-md-2"><button type="button" class="btn btn-danger" onclick="finishReservationQuickReservation(' + reservation.id + ')">Finalizar</button></div>';
              html += '</div>'
            });
            $("#divAlertActualReservations").show();
            $("#divActualReservations").append(html);
          }else{
            $("#btnSaveQuickReservation").prop('disabled', false);
          }
        }else{
          $("#btnSaveQuickReservation").prop('disabled', false);
        }

      },
      error: function() { // error handling
        Toast.fire({
          icon: 'error',
          title: 'Error al mostrar si tienes otras consultas actualmente.'
        });
      }
    });
  }

  function openStartQuickReservation(patientId = null) {
    patientIdQuickStart = patientId;
    if (patientIdQuickStart) {
      $('#quickReservationDiv').hide();
    } else {
      $('#quickReservationDiv').show();
    }

    $("#btnSaveQuickReservation").prop('disabled', true);
    $('#divAlertActualReservations').hide();
    validateLaboratoriesQuickReservation();
    validateOtherReservationsQuickReservation();
    //Abrir modaly cargar información
    $('#modalQuickReservation').modal('show');
  }

  function addQuickReservation() {

    if($('#divActualReservations').is(':empty')){
      let patient = ((patientIdQuickStart) ? patientIdQuickStart : $("#patientQuickReservation").val());
      if(patient && $('#laboratoryQuickReservation').val() && $("#categoryQuickReservation").val() &&  $("#areaQuickReservation").val()){
        $.ajax({
          url: "./?action=reservations/add-quick-reservation-patient", // json datasource
          type: "POST", // method, by default get
          data: {
            patient: patient,
            laboratory_id: $("#laboratoryQuickReservation").val(),
            category_id: $("#categoryQuickReservation").val(),
            area_id: $("#areaQuickReservation").val(),
            date: getDateQuickReservation(),
            timeAt: getStartTimeQuickReservation(),
            timeAtFinal: getEndTimeQuickReservation(),
            reservationId: 0
          },
          success: function(data) {
            window.location = "index.php?view=reservations/details&id=" + data;
          },
          error: function() { // error handling
            Toast.fire({
              icon: 'error',
              title: 'Error al agregar tu nueva consulta, intenta nuevamente o recarga la página.'
            });
          }
        });
      }else{
        Toast.fire({
            icon: 'error',
            title: 'Introduce toda la información para comenzar la consulta'
          });
      }
    }else{
       Toast.fire({
            icon: 'error',
            title: 'Tienes consultas activas. Finalízalas para continuar con tu nueva consulta.'
          });
    }
  }

  function finishReservationQuickReservation(reservationId) {
        $.ajax({
            url: "./?action=reservations/finish-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: reservationId
            },
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: 'Se ha marcado como finalizada la consulta.'
                });
                $("#divOtherRQuickReservation"+reservationId).remove();
                validateLaboratoriesQuickReservation();
                if($('#divActualReservations').is(':empty')){
                  $("#btnSaveQuickReservation").prop('disabled', false);
                  $('#divAlertActualReservations').hide();
                }else{
                  $("#btnSaveQuickReservation").prop('disabled', true);
                }
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al finalizar la consulta.'
                });
            }
        });
    }

    function getDateQuickReservation(){
      let currentDate = new Date();
      let day = currentDate.getDate();
      let month = currentDate.getMonth() + 1; // Los meses van de 0 a 11, así que sumamos 1
      let year = currentDate.getFullYear();

      // Asegurémonos de que el día y el mes tengan siempre dos dígitos
      if (day < 10) day = '0' + day;
      if (month < 10) month = '0' + month;

      return year + '-' + month + '-' + day;
    }

    function getStartTimeQuickReservation(){
      let currentDate = new Date();
      let hours = currentDate.getHours();
      let minutes = currentDate.getMinutes();

      // Asegurémonos de que las horas y los minutos tengan siempre dos dígitos
      if (hours < 10) hours = '0' + hours;
      if (minutes < 10) minutes = '0' + minutes;

      return hours + ':' + minutes;
    }
    
    function getEndTimeQuickReservation(){
      let currentDate = new Date();
      currentDate.setMinutes(currentDate.getMinutes() + 30);

      let hours = currentDate.getHours();
      let minutes = currentDate.getMinutes();

      // Asegurémonos de que las horas y los minutos tengan siempre dos dígitos
      if (hours < 10) hours = '0' + hours;
      if (minutes < 10) minutes = '0' + minutes;

      return hours + ':' + minutes;
    }
</script>