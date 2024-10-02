<?php
$user = UserData::getById($_GET["id"]);
$userTypes = UserTypeData::getAll();
?>

<div class="row">
  <div class="col-md-12">
    <h1>Editar Usuario</h1>
    <br>
    <form class="form-horizontal" method="post" id="addproduct" action="index.php?action=users/update" role="form">
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" value="<?php echo $user->name; ?>" class="form-control" id="name" placeholder="Nombre">
        </div>
      </div>

      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre de usuario*</label>
        <div class="col-md-6">
          <input type="text" name="username" value="<?php echo $user->username; ?>" class="form-control" required id="username" placeholder="Nombre de usuario">
        </div>
      </div>

      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Contraseña</label>
        <div class="col-md-6">
          <input type="password" name="password" class="form-control" id="inputEmail1" placeholder="Contrase&ntilde;a">
          <p class="help-block">La contraseña sólo se modificará si escribes algo, en caso contrario no se modifica.</p>
        </div>
      </div>

      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Tipo de usuario</label>
        <div class="col-md-6">
          <div>
            <select class="form-control" name="user_type" id="user_type">
              <option value="">-- SELECCIONE --</option>
              <?php foreach ($userTypes as $type) : ?>
                <option value="<?php echo $type->id; ?>" <?php echo ($user->user_type == $type->id) ? "selected" : "" ?>>
                  <?php echo $type->description; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <input type="hidden" name="user_id" value="<?php echo $user->id; ?>">
          <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        </div>
      </div>

    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    var tipoo = document.getElementById('tu').value;

    $('#tipo_usuario > option[value=tipoo]').attr('selected', 'selected');
  });
</script>