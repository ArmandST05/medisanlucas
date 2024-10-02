  <script src="assets/jquery-2.1.1.min.js" type="text/javascript"></script>
  <link href="assets/select2.min.css" rel="stylesheet" />
  <script src="assets/select2.min.js"></script>
  <?php
  $user = UserData::getLoggedIn();
  $userType = (isset($user)) ? $user->user_type : null;

  if ($userType == "do") {
    $medics = [MedicData::getByUserId($_SESSION["user_id"])];
  } else {
    $medics = MedicData::getAll();
  }

  $laboratories = LaboratoryData::getAll();

  $dateTime = isset($_GET["start"])  ? $_GET['start'] :  date("Y-m-d H:i:s");
  $date = date('Y-m-d', strtotime($dateTime));
  $timeAt = date('H:i', strtotime($dateTime));
  $timeAtFinal = strtotime('+30 minute',  strtotime($dateTime));
  $timeAtFinal = date('H:i', $timeAtFinal);
  ?>
  <div class="row">
    <div class="col-md-12">
      <h1>Nueva Cita Médico</h1>
      <br>
      <div class="box box-primary">
        <div class="box-body">
          <form class="form-horizontal" method="POST" action="./?action=reservations/add-reservation-medic" role="form">

            <div class="form-group">
              <div class="col-lg-3">
                <label for="inputEmail1" class="col-lg-3 control-label">Fecha</label>
                <input type="date" name="date" id="date" class="form-control" value="<?php echo $date ?>">
              </div>

              <div class="clearfix col-lg-2">
                <label for="inputEmail1" class="control-label">Hora Inicio</label>
                <input type="time" class="form-control" value="<?php echo $timeAt ?>" name="timeAt" id="timeAt" class="form-control">
              </div>

              <div class="clearfix col-lg-2">
                <label for="inputEmail1" class="control-label">Hora Fin</label>
                <input type="time" class="form-control" value="<?php echo $timeAtFinal ?>" name="timeAtFinal" id="timeAtFinal" class="form-control">
              </div>
              <div class="col-lg-5">
                <label for="inputEmail1" class="col-lg-3 control-label">Médico</label>
                <select name="medic" id="medic" class="form-control" id="combobox" autofocus required>
                  <option value="">-- SELECCIONE -- </option>
                  <?php foreach ($medics as $medic) : ?>
                    <option value="<?php echo $medic->id; ?>"><?php echo $medic->name ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <div class="col-lg-12">
                <label for="inputEmail1" class="col-lg-1 control-label">Nota</label>
                <textarea class="form-control" name="reason"></textarea>
              </div>
            </div>
            <div class="form-group">
              <div class="col-lg-2 pull-right">
                <button type="submit" class="btn btn-default">Agregar Cita</button>
                <input type="hidden" value="<?php echo $_SESSION["user_id"]; ?>" name="userId" id="userId">
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
  </script>