<?php
$reservation = ReservationData::getById($_GET["id"]);

if (!$reservation) {
  echo "<script>
      alert('La cita no existe');
      history.back();
    </script>";
}
$user = UserData::getLoggedIn();
$userType = $user->user_type;

$medics = MedicData::getAll();

$patient = PatientData::getById($reservation->patient_id);
$patients = PatientData::getAll();
$categories = ReservationData::getReservationCategories();
$selectedLaboratory = LaboratoryData::getById($reservation->laboratory_id);
$areas = ReservationAreaData::getAll();

$products = array_merge(ProductData::getAllByTypeId(1)); //Conceptos ingresos para venta
$selectedProducts = ReservationData::getProductsByReservation($reservation->id); //Productos seleccionados
?>

<div class="row">
  <div class="col-md-12">
    <h1>Editar Cita</h1>
    <br>
    <div class="box box-primary" id="patientDetails">
      <div class="box-header with-border">
        <h3 class="box-title">Datos del Paciente</h3>
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
          <b>Referido: </b><?php echo $patient->referred_by ?>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <div class="box box-primary">
      <div class="box-body">
        <form class="form-horizontal" method="POST" action="./?action=reservations/update-reservation-patient" role="form">
          <div class="form-group">
            <div class="col-md-4">
              <label class="control-label">Fecha</label>
              <input type="date" name="date" id="formDate" class="form-control" value="<?php echo $reservation->getDate() ?>" onchange="validateLaboratories()">
            </div>
            <div class="col-md-4">
              <label class="control-label">Hora Inicio</label>
              <input type="time" class="form-control" value="<?php echo $reservation->getStartTime() ?>" name="timeAt" id="timeAt" onchange="validateLaboratories()">
            </div>
            <div class="col-md-4">
              <label class="control-label">Hora Fin</label>
              <input type="time" class="form-control" value="<?php echo $reservation->getEndTime() ?>" name="timeAtFinal" id="timeAtFinal"onchange="validateLaboratories()"">
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-4">
              <label for="inputEmail1" class="col-md-3 control-label">Paciente</label>
              <select name="patient" id="patient" class="form-control" onchange="selectPatient()" autofocus required>
                <option value="" disabled selected>-- SELECCIONE -- </option>
                <?php foreach ($patients as $patient) : ?>
                  <option value="<?php echo $patient->id; ?>" <?php echo ($reservation->patient_id == $patient->id) ? "selected" : "" ?>><?php echo $patient->id . " - " . $patient->name ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="col-md-3 control-label">Médico</label>
              <select name="medic" id="medic" class="form-control" required>
                <?php if ($userType == "su" || $userType == "r") : ?>
                  <option value="" disabled selected>-- SELECCIONE --</option>
                <?php endif; ?>
                ?>
                <?php foreach ($medics as $medic) : ?>
                  <option value="<?php echo $medic->id; ?>" <?php echo ($reservation->medic_id == $medic->id) ? "selected" : "" ?>><?php echo $medic->id . " - " . $medic->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="col-md-3 control-label">Consultorio/Laboratorio</label>
              <select name="laboratory" id="laboratory" class="form-control" required>
                <option value='' disabled selected>-- SELECCIONE --</option>
                <?php foreach ($laboratories as $laboratory) : ?>
                  <option value="<?php echo $laboratory->id; ?>" <?php echo ($reservation->laboratory_id == $laboratory->id) ? "selected" : "" ?>><?php echo $laboratory->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-4">
              <label class="col-md-3 control-label">Categoría</label>
              <select name="category" id="category" class="form-control" required>
                <option value="" disabled selected>-- SELECCIONE --</option>
                <?php foreach ($categories as $category) : ?>
                  <option value="<?php echo $category->id; ?>" <?php echo ($reservation->category_id == $category->id) ? "selected" : "" ?>><?php echo $category->id . " - " . $category->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="col-md-3 control-label">Área</label>
              <select name="area" id="area" class="form-control" required>
                <option value="" disabled selected>-- SELECCIONE --</option>
                <?php foreach ($areas as $area) : ?>
                  <option value="<?php echo $area->id; ?>" <?php echo ($reservation->area_id == $area->id) ? "selected" : "" ?>><?php echo $area->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <label class="control-label">Motivo de la Consulta:</label>
              <textarea class="form-control" name="reason" id="reason" placeholder="Motivo de la Consulta"><?php echo $reservation->reason; ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-12">
              <label class="control-label">Servicios a realizar</label>
              <label id="lbTotalProducts">(Total: $0.00)
                <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="Puedes indicar los servicios o conceptos/ingresos para posteriormente cobrarlos."></i>
              </label>
              <select name="products[]" id="products" class="form-control" t data-tags="true" data-allow-clear="true">
                <?php foreach ($products as $product) : ?>
                  <option value="<?php echo $product->id; ?>" data-price-out="<?php echo $product->price_out; ?>"><?php echo $product->name. " | $" . number_format($product->price_out,2); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-2 pull-right">
              <button type="submit" class="btn btn-default"> Guardar Cita</button>
              <input type="hidden" value="<?php echo $_SESSION["user_id"]; ?>" name="userId">
              <input type="hidden" value="<?php echo $reservation->id; ?>" name="reservationId">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
<script type="text/javascript">
  $(document).ready(function() {
    $("#patient").select2({});
    $("#medic").select2({});
    $("#laboratory").select2({});
    $("#category").select2({});
    $("#area").select2({});
    $("#products").select2({
      multiple: true,
      allowClear: true,
      templateSelection:function(data,container){
        $(data.element).attr("data-price-out",data.priceOut);
        return data.text;
      }
    });
    $("#products").val(null).trigger("change");

    //Cargar datos por defecto en el select productos
    let selectedProducts = JSON.parse('<?php echo json_encode($selectedProducts) ?>');
    let selectedProductsArray = [];

    $(selectedProducts).each(function() {
      selectedProductsArray.push(this.product_id);
    });

    $("#products").val(selectedProductsArray).trigger("change");
    calculateTotalProducts();

    $('#products').on('select2:select', function (e) {
      calculateTotalProducts();
    });
    $('#products').on('select2:unselect', function (e) {
      calculateTotalProducts();
    });
    $('#products').on('select2:clear', function (e) {
      calculateTotalProducts();
    });
    
    validateLaboratories();
  });

  function calculateTotalProducts(){
    //Calcula el total a pagar por los productos(conceptos/ingresos) seleccionados
    let totalProducts = 0;
    let selectedProducts = $('#products').select2('data');

    $(selectedProducts).each(function(index,product){
      let value = product.element.attributes['data-price-out'].nodeValue;
      totalProducts += ((isNaN(parseFloat(value))) ? 0 : parseFloat(value));
    });

    $("#lbTotalProducts").text("Total: $"+parseFloat(totalProducts).toFixed(2));
  }

  function selectPatient() {
    $("#patientDetails").hide();
  }

  function validateLaboratories() {
    $.ajax({
      url: "./?action=laboratories/validate-availability", // json datasource
      type: "POST", // method, by default get
      data: {
        date: $("#formDate").val(),
        timeAt: $("#timeAt").val(),
        timeAtFinal: $("#timeAtFinal").val(),
        reservationId: "<?php echo $reservation->id?>"
      },
      success: function(data) {
        $('#laboratory').select2('destroy');
        $('#laboratory').empty();
        let laboratories = JSON.parse(data);
        let html = '<option value="" disabled selected>-- SELECCIONE --</option>';
        $("#laboratory").append(html);

        $.each(laboratories, function(index, laboratory) {
          let selected = "";
          if(laboratory.id == "<?php echo $selectedLaboratory->id ?>"){
            selected = "selected";
          }
          let html = '<option value="' + laboratory.id + '" '+selected+'>' + laboratory.name + '</option>';
          $("#laboratory").append(html);
        });
       $('#laboratory').select2({});

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