<?php
$reservation = ReservationData::getById($_GET["id"]);
$user = UserData::getLoggedIn();
$userType = (isset($user)) ? $user->user_type : null;

//Obtener médicos
if ($userType == "do") {
  $medics = [MedicData::getByUserId($_SESSION["user_id"])];
} else {
  $medics = MedicData::getAll();
}
?>
<div class="row">
  <div class="col-md-12">
    <h1>Editar Cita Médico</h1>
    <br>
    <div class="box box-primary">
      <div class="box-header with-border">
        <div class="pull-right">
          <button id="btnDeleteReservation" class='btn btn-danger btn-xs'><i class="fas fa-trash"></i> Eliminar</button>
        </div>
      </div>
      <div class="box-body">
        <form class="form-horizontal" method="POST" action="./?action=reservations/update-reservation-medic" role="form">
          <div class="form-group ">
            <div class="col-lg-3">
              <label for="inputEmail1" class="control-label">Fecha</label>
              <input type="date" name="date" id="formfecha" class="form-control" value="<?php echo $reservation->getDate() ?>">
            </div>
            <div class="clearfix col-lg-2">
              <label for="inputEmail1" class="control-label">Hora Inicio</label>
              <input type="time" class="form-control" value="<?php echo $reservation->getStartTime() ?>" name="timeAt" id="timeAt" class="form-control">
            </div>
            <div class="clearfix col-lg-2">
              <label for="inputEmail1" class="control-label">Hora Fin</label>
              <input type="time" class="form-control" value="<?php echo $reservation->getEndTime() ?>" name="timeAtFinal" id="timeAtFinal" class="form-control">
            </div>
            <div class="col-lg-5">
              <label for="inputEmail1" class="col-lg-3 control-label">Médico</label>
              <select name="medic" class="form-control" id="medic" required>
                <option value="">-- SELECCIONE --</option>
                <?php foreach ($medics as $medic) : ?>
                  <option value="<?php echo $medic->id; ?>" <?php echo ($medic->id == $reservation->medic_id) ? "selected" : ""; ?>><?php echo $medic->id . " - " . $medic->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-12">
              <label for="inputEmail1" class="col-lg-1 control-label">Nota</label>
              <textarea class="form-control" name="reason"><?php echo $reservation->reason; ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-2 pull-right">
              <input type="hidden" name="id" value="<?php echo $reservation->id; ?>">
              <input type="hidden" value="<?php echo  $_SESSION["user_id"]; ?>" name="userId" id="userId">
              <button type="submit" class="btn btn-default">Actualizar Cita</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#medic").select2({});
  });


  $("#btnDeleteReservation").click(function() {
    Swal.fire({
      title: '¿Estás seguro?',
      text: "¡No serás capaz de revertir esto!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Sí, Eliminar'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "./?action=reservations/delete-reservation", // json datasource
          type: "POST", // method, by default get
          data: "id=" + "<?php echo $_GET["id"]; ?>",
          success: function() {
            window.location = 'index.php?view=home';
          },
          error: function() { // error handling
            Swal.fire(
              'Error',
              'La cita no se ha podido eliminar.',
              'error'
            );
          }
        });
      }
    })
  });
</script>