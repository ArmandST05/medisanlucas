<?php
$months = ["1" => "Enero", "2" => "Febrero", "3" => "Marzo", "4" => "Abril", "5" => "Mayo", "6" => "Junio", "7" => "Julio", "8" => "Agosto", "9" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];
$patient = PatientData::getById($_GET["id"]);

$categories = PatientData::getPatientCategories();
?>
<script src="plugins/croppie/js/croppie.js"></script>
<link rel="stylesheet" href="plugins/croppie/css/croppie.css" />

<div class="row">
  <div class="col-md-12">
    <h1>Editar Paciente</h1>
    <br>
    <div class="box box-primary">
      <!-- /.box-header -->
      <div class="box-body">
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=patients/update" role="form">
          <div class="form-group">
            <label class="col-lg-2 control-label">Foto de perfil:</label>
            <div class="col-md-6">
              <div id="image_profile" style="width:300px; margin-top:10px"></div>
              <label for="insert_image">
                <a class="btn btn-success" style="margin-left:31px;">Seleccionar </a>
              </label>
              <button type="button" id="rotate_image" class="btn btn-primary rotate_image" data-deg="-90"><span class="glyphicon glyphicon-repeat"></span> Rotar</button>
              <button type="button" id="reset_image" class="btn btn-danger" data-deg="-90"><span class="glyphicon glyphicon-cancel"></span>Eliminar</button>
              <input id="insert_image" type="file" style='display: none;' name="image" accept="image/*" />
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Nombre*</label>
            <div class="col-md-6">
              <input type="text" name="name" value="<?php echo $patient->name; ?>" class="form-control" id="name" placeholder="Nombre">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Sexo:*</label>
            <div class="col-md-6">
              <select id="sex" name="sex" class="form-control">
                <option value="1" <?php echo ($patient->sex_id == 1) ? "selected" : "" ?>>Masculino</option>
                <option value="2" <?php echo ($patient->sex_id == 2) ? "selected" : "" ?>>Femenino</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">CURP:</label>
            <div class="col-md-6">
              <input type="text" name="curp" value="<?php echo $patient->curp; ?>" class="form-control" id="curp" placeholder="CURP">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Nombre del familiar:</label>
            <div class="col-md-6">
              <input type="text" name="relative_name" value="<?php echo $patient->relative_name; ?>" class="form-control" id="relative_name" placeholder="Nombre del familiar" autofocus>
            </div>
          </div>

          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Dirección:</label>
            <div class="col-lg-3">
              <input type="text" id="street" name="street" value="<?php echo $patient->street; ?>" class="form-control" placeholder="Calle">
            </div>
            <div class="col-lg-1">
              <input type="text" id="number" name="number" value="<?php echo $patient->number; ?>" class="form-control" placeholder="Número">
            </div>
            <div class="col-lg-2">
              <input type="text" id="colony" name="colony" value="<?php echo $patient->colony; ?>" class="form-control" placeholder="Colonia">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Teléfonos:</label>
            <div class="col-lg-2">
              <input type="text" id="cellphone" name="cellphone" value="<?php echo $patient->cellphone; ?>" class="form-control" placeholder="Teléfono">
            </div>
            <div class="col-lg-2">
              <input type="text" id="homephone" name="homephone" value="<?php echo $patient->homephone; ?>" class="form-control" placeholder="Tel alternativo">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Email:</label>
            <div class="col-md-6">
              <input type="text" id="email" name="email" value="<?php echo $patient->email; ?>" class="form-control" placeholder="Email">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Fecha nacimiento:</label>
            <div class="col-lg-6">
              <input type="date" name="birthday" id="birthday" value="<?php echo $patient->birthday; ?>" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Referido por:*</label>
            <div class="col-md-6">
              <input type="text" id="referred_by" name="referred_by" value="<?php echo $patient->referred_by; ?>" class="form-control" id="email1" placeholder="Referido por">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-lg-2 control-label">Categoría</label>
            <div class="col-md-6">
              <select id="category_id" name="category_id" class="form-control" required>
                <?php foreach ($categories as $category) : ?>
                  <option value="<?php echo $category->id; ?>" <?php echo ($patient->category_id == $category->id) ? "selected" : ""; ?>>
                    <?php echo $category->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <input type="hidden" id="user_id" name="user_id" value="<?php echo $patient->id; ?>">
              <button type="button" class="btn btn-primary" id="updatePatient">Actualizar Paciente</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
</div>

<script>
  $(document).ready(function() {
    var image = "<?php echo $patient->image ?>";
    var url_image = 'storage_data/patients/' + image;
    if (image == '') {
      $('#image_profile').hide();
      $('#rotate_image').hide();
      $('#reset_image').hide();
    }

    $image_crop = $('#image_profile').croppie({
      enableExif: true,
      enableOrientation: true,
      viewport: {
        width: 150,
        height: 150,
        type: 'square'
      },
      boundary: {
        width: 150,
        height: 150
      }
    });

    $("#image_profile").croppie('bind', {
      url: url_image,
      points: [100, 100, 100, 100]
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
    });

    $('#rotate_image').on('click', function(ev) {
      $image_crop.croppie('rotate', parseInt($(this).data('deg')));
    });

    $('#updatePatient').click(function(event) {
      var user_id = $("#user_id").val();
      var name = $("#name").val();
      var sex = $("#sex").val();
      var curp = $("#curp").val();
      var relative_name = $("#relative_name").val();
      var street = $("#street").val();
      var number = $("#number").val();
      var colony = $("#colony").val();
      var cellphone = $("#cellphone").val();
      var homephone = $("#homephone").val();
      var birthday = $("#birthday").val();
      var email = $("#email").val();
      var referred_by = $("#referred_by").val();
      var category_id = $("#category_id").val();

      $image_crop.croppie('result', {
        type: 'canvas',
        size: 'viewport'
      }).then(function(response) {
        var image = response;
        $.ajax({
          url: "./?action=patients/update",
          type: 'POST',
          data: {
            "user_id": user_id,
            "name": name,
            "sex": sex,
            "curp": curp,
            "relative_name": relative_name,
            "street": street,
            "number": number,
            "colony": colony,
            "cellphone": cellphone,
            "homephone": homephone,
            "birthday": birthday,
            "email": email,
            "referred_by": referred_by,
            "category_id": category_id,
            "image": image
          },
          success: function(data, textStatus, xhr) {
            window.location = "index.php?view=patients/index";
          },
          error: function() {
            alert("Ha ocurrido un error al almacenar los datos.");
          }
        });

      });

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
</script>