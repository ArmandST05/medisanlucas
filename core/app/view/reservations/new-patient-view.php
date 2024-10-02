<?php
$patients = PatientData::getAll();
$user = UserData::getLoggedIn();
$userType = $user->user_type;

if ($userType == "do") {
  $medicData = MedicData::getByUserId($_SESSION["user_id"]);
  $defaultMedic =  $medicData->id;
}else{
  $defaultMedic =  0;
}
  $medics = MedicData::getAll();
   //$medics = [MedicData::getByUserId($_SESSION["user_id"])];

$categories = ReservationData::getReservationCategories();

$laboratories = LaboratoryData::getByStatus(1);
$areas = ReservationAreaData::getAll();

$dateTime = isset($_GET["start"])  ? $_GET['start'] :  date("Y-m-d H:i:s");

$date = date('Y-m-d', strtotime($dateTime));
$timeAt = date('H:i', strtotime($dateTime));
$timeAtFinal = strtotime('+30 minute',  strtotime($dateTime));
$timeAtFinal = date('H:i', $timeAtFinal);

$products = array_merge(ProductData::getAllByTypeId(1)); //Conceptos ingresos para venta
?>
<div class="row">
  <div class="col-md-12">
    <h1>Nueva Cita</h1>
    <br>
    <div class="box box-primary">
      <div class="box-body">
        <form class="form-horizontal" method="POST" action="./?action=reservations/add-reservation-patient" role="form">
          <div class="form-group">
            <div class="col-lg-4">
              <label for="inputEmail1" class="control-label">Fecha</label>
              <input type="date" name="date" id="formDate" class="form-control" value="<?php echo $date ?>" onchange="validateLaboratories()">
            </div>
            <div class="clearfix col-lg-4">
              <label for="inputEmail1" class="control-label">Hora Inicio</label>
              <input type="time" class="form-control" value="<?php echo $timeAt ?>" name="timeAt" id="timeAt" onchange="validateLaboratories()">
            </div>
            <div class="clearfix col-lg-4">
              <label for="inputEmail1" class="control-label">Hora Fin</label>
              <input type="time" class="form-control" value="<?php echo $timeAtFinal ?>" name="timeAtFinal" id="timeAtFinal" onchange="validateLaboratories()">
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-4">
              <label for="inputEmail1" class="col-md-3 control-label">Paciente</label>
              <select name="patient" id="patient" class="form-control" autofocus required>
                <option value="" disabled selected>-- SELECCIONE -- </option>
                <?php foreach ($patients as $patient) : ?>
                  <option value="<?php echo $patient->id; ?>"><?php echo $patient->id . " - " . $patient->name ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="col-md-3 control-label">Médico</label>
              <select name="medic" id="medic" class="form-control" required>
                <?php if ($userType == "su" || $userType == "r") : ?>
                  <option value="" disabled selected>-- SELECCIONE --</option>
                <?php endif; ?>
                <?php foreach ($medics as $medic) : ?>
                  <option value="<?php echo $medic->id; ?>" <?php ($defaultMedic== $medic->id) ? "selected":""?>><?php echo $medic->id . " - " . $medic->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="col-md-3 control-label">Consultorio/Laboratorio</label>
              <select name="laboratory" id="laboratory" class="form-control" required>
              </select>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-4">
              <label class="col-md-3 control-label">Categoría</label>
              <select name="category" id="category" class="form-control" required>
                <option value="" disabled selected>-- SELECCIONE --</option>
                <?php foreach ($categories as $category) : ?>
                  <option value="<?php echo $category->id; ?>"><?php echo $category->id . " - " . $category->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="col-md-3 control-label">Área</label>
              <select name="area" id="area" class="form-control" required>
                <option value="" disabled selected>-- SELECCIONE --</option>
                <?php foreach ($areas as $area) : ?>
                  <option value="<?php echo $area->id; ?>"><?php echo $area->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-12">
              <label for="inputEmail1" class="control-label">Motivo de la Consulta:</label>
              <textarea class="form-control" name="reason" id="reason" placeholder="Motivo de la Consulta"></textarea>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-12">
              <label class="control-label">Servicios a realizar</label>
              <label id="lbTotalProducts">(Total: $0.00)
                <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="Puedes indicar los servicios o conceptos/ingresos para posteriormente cobrarlos."></i>
              </label>
              <select name="products[]" id="products" class="form-control">
                <?php foreach ($products as $product) : ?>
                  <option value="<?php echo $product->id; ?>" data-price-out="<?php echo $product->price_out; ?>"><?php echo $product->name . " | $" . number_format($product->price_out, 2); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-2 pull-right">
              <button type="submit" class="btn btn-default">Guardar Cita</button>
              <input type="hidden" value="<?php echo $_SESSION["user_id"]; ?>" name="userId" id="userId">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
<script type="text/javascript">
  var Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000
  });

  $(document).ready(function() {
    $("#laboratory").select2({});
    $("#patient").select2({});
    $("#medic").select2({});
    $("#category").select2({});
    $("#area").select2({});
    $("#products").select2({
      multiple: true,
      allowClear: true
    });
    $('#products').val(null).trigger('change');

    $('#products').on('select2:select', function(e) {
      calculateTotalProducts();
    });
    $('#products').on('select2:unselect', function(e) {
      calculateTotalProducts();
    });
    $('#products').on('select2:clear', function(e) {
      calculateTotalProducts();
    });

    validateLaboratories();
  });

  function calculateTotalProducts() {
    //Calcula el total a pagar por los productos(conceptos/ingresos) seleccionados
    let totalProducts = 0;
    let selectedProducts = $('#products').select2('data');

    $(selectedProducts).each(function(index,product) {
      let value = product.element.attributes['data-price-out'].nodeValue;
      totalProducts += ((isNaN(parseFloat(value))) ? 0 : parseFloat(value));
    });

    $("#lbTotalProducts").text("Total: $" + parseFloat(totalProducts).toFixed(2));
  }

  function validateLaboratories() {
    $.ajax({
      url: "./?action=laboratories/validate-availability", // json datasource
      type: "POST", // method, by default get
      data: {
        date: $("#formDate").val(),
        timeAt: $("#timeAt").val(),
        timeAtFinal: $("#timeAtFinal").val(),
        reservationId: 0
      },
      success: function(data) {
        $('#laboratory').select2('destroy');
        $('#laboratory').empty();
        let laboratories = JSON.parse(data);
        let html = '<option value="" disabled selected>-- SELECCIONE --</option>';
        $("#laboratory").append(html);

        $.each(laboratories, function(index, laboratory) {
          let html = '<option value="' + laboratory.id + '">' + laboratory.name + '</option>';
          $("#laboratory").append(html);
        });
        $('#laboratory').select2();

      },
      error: function() { // error handling
        Toast.fire({
          icon: 'error',
          title: 'Error al mostrar los consultorios disponibles, recarga la página.'
        });
      }
    });
  }
</script>