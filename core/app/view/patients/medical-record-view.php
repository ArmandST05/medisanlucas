<?php
$configuration = ConfigurationData::getAll();
$patientId = $_GET["patientId"];
$patient = PatientData::getById($patientId);
$lastVitalSigns = ExplorationExamData::getLastByPatientType($patientId, 1);
$lastVitalSignsArray = array_chunk($lastVitalSigns, 2);
$recordSections = RecordSectionData::getRecordsByPatient($patientId);
$recordSectionsArray = array_chunk($recordSections, 2);
$reservationsHistory = ReservationData::getByPatient($_GET["patientId"], 2);
$totResHistory = count($reservationsHistory);
?>
<input type="hidden" id="patientId" name="patientId" value="<?php echo $_GET["patientId"] ?>">
<div class="row">
  <div class="col-lg-12">
    <h1>Expediente del paciente</h1>
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Datos del Paciente</h3>
        <div class="pull-right">
          <a href='./?view=patients/medical-record-report&id=<?php echo $_GET["patientId"] ?>' target="_blank" class='btn btn-default btn-xs'><i class="fas fa-file-alt"></i> Exportar expediente</a>
          <a href='./?view=patients/edit&id=<?php echo $_GET["patientId"] ?>' class='btn btn-primary btn-xs'><i class="fas fa-pencil-alt"></i> Editar</a>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="col-md-3">
          <img class="profile-user-img img-responsive img-circle" src='<?php echo ($patient->image) ? "storage_data/patients/" . $patient->image : "../../../assets/default_user.jpg" ?>' alt="Foto del paciente">
        </div>
        <div class="col-md-9">
          <b>Nombre completo: </b><?php echo $patient->name ?><br>
          <b>Dirección: </b><?php echo $patient->street ?> <?php echo $patient->number ?>
          <?php echo $patient->colony ?><br>
          <b>Teléfono: </b><?php echo $patient->cellphone ?> <br><b>Teléfono alternativo:
          </b><?php echo $patient->homephone ?><br>
          <b>Email: </b><?php echo $patient->email ?><br>
          <b>Fecha nacimiento: </b><?php echo $patient->getBirthdayFormat() ?><br>
          <b>Edad: </b><?php echo $patient->getAge() ?><br>
          <b>Referido por: </b><?php echo $patient->referred_by ?>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">ÚLTIMOS SIGNOS VITALES</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <?php foreach ($lastVitalSignsArray as $vitalSigns) : ?>
          <div class="row">
            <?php foreach ($vitalSigns as $vitalSign) : ?>
              <div class="col-md-3">
                <label><?php echo $vitalSign->name ?></label>
              </div>
              <div class="col-md-3">
                <?php echo ($vitalSign->value) ? $vitalSign->value : "--" ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">ANTECEDENTES</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <?php foreach ($recordSectionsArray as $recordSections) : ?>
          <div class="row">
            <?php foreach ($recordSections as $recordSection) : ?>
              <div class="col-md-6">
                <b><?php echo $recordSection->name ?></b>
                <textarea class="form-control" id="record<?php echo $recordSection->id; ?>" onkeyup="updateRecordSection('<?php echo $recordSection->id ?>')"><?php echo trim($recordSection->value) ?></textarea>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">OBSERVACIONES</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <textarea class="form-control" id="notes" rows="10">
              <?php echo $patient->notes ?>
            </textarea>
          </div>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">HISTORIAL</h3>
        <span title="3 New Messages" class="badge badge-primary"><?php echo $totResHistory ?> CONSULTAS</span>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table class="table table-bordered table-hover table-responsive" id="table">
          <h5>
            <?php if ($totResHistory == 1) echo $totResHistory . " Resultado";
            else echo $totResHistory . " Resultados";
            ?></h5>
          <thead>
            <th>Fecha/Hora</th>
            <th>Médico</th>
            <th>Motivo</th>
            <th>Archivos anexados</th>
            <th>Receta</th>
            <th>Acciones</th>
          </thead>
          <?php
          foreach ($reservationsHistory as $reservation) :
            $medic = $reservation->getMedic();
            $files = PatientData::getAllFilesByPatientReservation($patient->id, $reservation->id);
          ?>
            <tr>
              <td><?php echo $reservation->day_name . " " . $reservation->date_at_format; ?></td>
              <td><?php echo $medic->name; ?></td>
              <td><?php echo $reservation->reason; ?></td>
              <td>
                <?php foreach ($files as $file) : ?>
                  <a href="storage_data/files/<?php echo $file->path ?>" target="__blank" class="btn btn-default btn-sm"><i class="fas fa-eye"></i><?php echo $file->path ?></a>
                <?php endforeach; ?>
              </td>
              <td>
                <?php if ($configuration['active_personalized_prescription']->value == 1) : ?>
                  <a href='./?view=reservations/report-prescription-personalized&id=<?php echo $reservation->id ?>' target="__blank" class='btn btn-primary btn-xs'><i class="fas fa-file-medical"></i> Receta Médica</a>
                <?php else : ?>
                  <a href='./?view=reservations/report-prescription&id=<?php echo $reservation->id ?>' target="__blank" class='btn btn-primary btn-xs'><i class="fas fa-file-medical"></i> Receta Médica</a>
                <?php endif; ?>
              </td>
              <td>
                <a href="index.php?view=reservations/details&id=<?php echo $reservation->id ?>" target="__blank" class="btn btn-default btn-xs"><i class='fas fa-align-justify'></i> Detalles de la cita</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
</div>
<script type='text/javascript'>
  var Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000
  });

  var notesNicEditor = new nicEditor().panelInstance('notes');

  $(document).ready(function() {
    $("div.nicEdit-main").keyup(function() {
      updateNotes();
    });
  });

  /*--------------RECORD SECTIONS---------------- */
  function updateRecordSection(id) {
    //Se creó una función sólo para los signos vitales
    let value = $('#record' + id).val();

    $.ajax({
      url: "./?action=record-sections/update-patient", // json datasource
      type: "POST", // method, by default get
      data: {
        recordSectionId: id,
        patientId: $("#patientId").val(),
        value: value
      },
      success: function() {
        Toast.fire({
          icon: 'success',
          title: 'Información Actualizada.'
        });
      },
      error: function() { // error handling
        Toast.fire({
          icon: 'error',
          title: 'Error al actualizar la información.'
        });
      }
    });
  }

  /*---------------PATIENT NOTES----------------- */

  function updateNotes() {
    let notes = nicEditors.findEditor('notes').getContent();
    $.ajax({
      url: "./?action=patients/update-medical-record-notes", // json datasource
      type: "POST", // method, by default get
      data: {
        patientId: $("#patientId").val(),
        notes: notes
      },
      success: function() {
        Toast.fire({
          icon: 'success',
          title: 'Actualizadas observaciones del paciente.'
        });
      },
      error: function() { // error handling
        Toast.fire({
          icon: 'error',
          title: 'Error al actualizar las observaciones del paciente.'
        });
      }
    });
  }
</script>