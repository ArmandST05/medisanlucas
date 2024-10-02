<?php $categories = PatientData::getAllCategories(); 
include_once($_SERVER['DOCUMENT_ROOT']."/core/app/view/reservations/new-patient-quick.php");
?>
<script src="plugins/croppie/js/croppie.js"></script>
<link rel="stylesheet" href="plugins/croppie/css/croppie.css" />

<div class="row">
  <div class="col-md-12">
    <h1>Nuevo Paciente</h1>
    <br>
    <div class="box box-primary">
      <!-- /.box-header -->
      <div class="box-body">
        <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addpatient" action="index.php?action=patients/add" role="form">

          <div class="form-group">
            <label class="col-lg-2 control-label">Foto de perfil:</label>
            <div class="col-md-6">
              <div id="image_profile" style="width:300px; margin-top:10px"></div>
              <label for="insert_image">
                <a class="btn btn-success" style="margin-left:31px;">Seleccionar foto</a>
              </label>
              <button type="button" id="rotate_image" class="btn btn-primary rotate_image" data-deg="-90"><span class="glyphicon glyphicon-repeat"></span> Rotar</button>
              <button type="button" id="reset_image" class="btn btn-danger" data-deg="-90"><span class="glyphicon glyphicon-cancel"></span>Eliminar</button>
              <input id="insert_image" type="file" style='display: none;' name="image" accept="image/*" />
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Nombre:*</label>
            <div class="col-md-8">
              <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre" autofocus>
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Sexo:*</label>
            <div class="col-md-8">
              <select id="sex" name="sex" class="form-control">
                <option value="1" selected>Masculino</option>
                <option value="2">Femenino</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">CURP:</label>
            <div class="col-md-8">
              <input type="text" name="curp" required class="form-control" id="curp" placeholder="CURP" autofocus>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Nombre de Familiar:</label>
            <div class="col-md-8">
              <input type="text" name="relative_name" class="form-control" id="relative_name" placeholder="Nombre del familiar" autofocus>
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Dirección:</label>
            <div class="col-lg-3">
              <input type="text" id="street" name="street" class="form-control" placeholder="Calle">
            </div>
            <div class="col-lg-2">
              <input type="text" id="number" name="number" class="form-control" placeholder="No.">
            </div>
            <div class="col-lg-3">
              <input type="text" id="colony" name="colony" class="form-control" placeholder="Colonia">
            </div>

          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Teléfonos:</label>
            <div class="col-lg-2">
              <input type="text" id="cellphone" name="cellphone" class="form-control" placeholder="Celular">
            </div>
            <div class="col-lg-2">
              <input type="text" id="homephone" name="homephone" class="form-control" placeholder="Teléfono Fijo">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Correo Electrónico:</label>
            <div class="col-md-8">
              <input type="text" id="email" name="email" class="form-control" placeholder="Correo Electrónico">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Fecha nacimiento:</label>
            <div class="col-lg-8">
              <input type="date" name="birthday" id="birthday" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Referido por:</label>
            <div class="col-md-8">
              <input type="text" id="referred_by" name="referred_by" class="form-control" placeholder="Referido por">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Categoría*</label>
            <div class="col-md-8">
              <select id="category_id" name="category_id" class="form-control" required>
                <?php foreach ($categories as $category) : ?>
                  <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-4 pull-right">
              <button type="button" onclick="addPatient(0)" class="btn btn-primary">Agregar</button><br><br>
              <button type="button" onclick="addPatient(1)" class="btn btn-default">Agregar e Iniciar consulta</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  var newPatientId = null;

  $(document).ready(function() {
    $('#image_profile').hide();
    $('#rotate_image').hide();
    $('#reset_image').hide();

    $image_crop = $('#image_profile').croppie({
      enableExif: true,
      enableOrientation: true,
      viewport: {
        width: 150,
        height: 150,
        type: 'square' //square
      },
      boundary: {
        width: 150,
        height: 150
      },

    });

    $('#insert_image').on('change', function() {

      $('#image_profile').show();
      $('#rotate_image').show();
      $('#reset_image').show();

      var reader = new FileReader();

      reader.onload = function(event) {
        $image_crop.croppie('bind', {
          url: event.target.result
        }).then(function() {});
      }

      reader.readAsDataURL(this.files[0]);
      $('#insertimageModal').modal('show');

    });

    $('#rotate_image').on('click', function(ev) {
      $image_crop.croppie('rotate', parseInt($(this).data('deg')));
    });

    $("#reset_image").click(function() {
      $('#image_profile').hide();
      $('#rotate_image').hide();
      $('#reset_image').hide();

      $('#insert_image').val(''); // this will clear the input val.
      $image_crop.croppie('bind', {
        url: ''
      }).then(function() {

      });
    });

  });

  function addPatient(isQuickReservation = 0) {
    if (!newPatientId) { //Si no se ha agregado el paciente, agregarlo.
      $image_crop.croppie('result', {
        type: 'canvas',
        size: 'viewport'
      }).then(function(response) {
        var image = response;
        $.ajax({
          url: "./?action=patients/add",
          type: 'POST',
          data: {
            "name": $("#name").val(),
            "sex": $("#sex").val(),
            "curp": $("#curp").val(),
            "street": $("#street").val(),
            "number": $("#number").val(),
            "colony": $("#colony").val(),
            "cellphone": $("#cellphone").val(),
            "homephone": $("#homephone").val(),
            "email": $("#email").val(),
            "birthday": $("#birthday").val(),
            "referred_by": $("#referred_by").val(),
            "category_id": $("#category_id").val(),
            "relative_name": $("#relative_name").val(),
            "image": image
          },
          success: function(data, textStatus, xhr) {
            newPatientId = data; //Asignar el nuevo paciente que se creó

            if (isQuickReservation) { //Si se requiere cita rápida
              openStartQuickReservation(newPatientId)

            } else { //Solo guardar al paciente y redireccionar
              window.location = "index.php?view=patients/index";
            }
          },
          error: function() {
            alert("Ha ocurrido un error al almacenar los datos.");
          }
        });

      });
    } else {
      if (isQuickReservation) { //Si se requiere cita rápida
        openStartQuickReservation(data)
      } else { //Solo guardar al paciente y redireccionar
        alert("Este paciente ya existe.");
      }
    }
  }
</script>