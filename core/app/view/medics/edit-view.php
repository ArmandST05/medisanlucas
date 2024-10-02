<?php
$medic = MedicData::getById($_GET["id"]);
$categories = CategoryMedicData::getAll();
$users = UserData::getUnassigned();
array_push($users, $medic->getUser());
?>
<div class="row">
  <div class="col-md-12">
    <h1>Editar Médico</h1>
    <br>
    <div class="box box-primary">
      <div class="box-body">
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=medics/update" role="form">
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Especialidad*</label>
            <div class="col-md-6">
              <select name="category_id" class="form-control">
                <option value="">-- SELECCIONE --</option>
                <?php foreach ($categories as $category) : ?>
                  <option value="<?php echo $category->id; ?>" <?php echo ($medic->category_id == $category->id) ? "selected" : "" ?>>
                    <?php echo $category->name; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
            <div class="col-md-6">
              <input type="text" name="name" value="<?php echo $medic->name; ?>" class="form-control" id="name" placeholder="Nombre">
            </div>
          </div>

          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Cédula Profesional*</label>
            <div class="col-md-6">
              <input type="text" name="professional_license" class="form-control" id="professional_license" value="<?php echo $medic->professional_license ?>" placeholder="Cédula Profesional">
            </div>
          </div>

          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Centro de Estudios*</label>
            <div class="col-md-6">
              <input type="text" name="study_center" class="form-control" id="study_center" value="<?php echo $medic->study_center ?>" placeholder="Centro de Estudios">
            </div>
          </div>

          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Logo Centro de Estudios</label>
            <div class="col-lg-2">
              <div class="checkbox">
                <label>
                  <input type="checkbox" id="isStudyCenterPresc" name="isStudyCenterPrescription" value="1" <?php echo ($medic->is_study_center_prescription == 1) ? "checked" : "" ?> onclick="selectedStudyCenterLogoPresc()">
                  Utilizar logo en la receta
                </label>
              </div>
            </div>
            <div class="col-md-3 divStudyCenterPresc" <?php echo ($medic->is_study_center_prescription == 1) ? '' : "style='display: none;'"; ?>>
              <input type="file" name="studyCenterLogo" class="form-control" id="studyCenterLogo">
            </div>
            <?php if($medic->is_study_center_prescription == 1):?>
            <div class="col-md-1 divStudyCenterPresc">
              <a class="btn btn-sm btn-default" target="_blank" href="storage_data/medics/<?php echo $medic->id?>/studyCenterLogo.png"><i class="fas fa-eye"></i></a>
            </div>
            <?php endif;?>
          </div>

          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Diplomados, estudios y/o especialidades extras (La información se incluirá en la receta)</label>
            <div class="col-md-6">
              <textarea name="otherSpecialties" class="form-control" id="otherSpecialties" rows="3"><?php echo $medic->other_specialties ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Correo Electrónico</label>
            <div class="col-md-6">
              <input type="text" name="email" class="form-control" id="email" value="<?php echo $medic->email ?>" placeholder="Correo Electrónico">
            </div>
          </div>

          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Teléfono</label>
            <div class="col-md-6">
              <input type="text" name="phone" class="form-control" id="phone" value="<?php echo $medic->phone ?>" placeholder="Teléfono">
            </div>
          </div>


          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Usuario</label>
            <div class="col-md-6">
              <select name="user_id" class="form-control">
                <option value="">-- SELECCIONE --</option>
                <?php foreach ($users as $user) : ?>
                  <option value="<?php echo $user->id; ?>" <?php echo ($medic->user_id == $user->id) ? "selected" : "" ?>>
                    <?php echo $user->name; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Color*</label>
            <div class="col-md-6">
              <input id="color" name="calendar_color" type="color" value="<?php echo $medic->calendar_color ?>" required>
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-6 pull-right">
              <input type="hidden" name="id" value="<?php echo $medic->id; ?>">
              <button type="submit" class="btn btn-primary">Actualizar Médico</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {});

  function selectedStudyCenterLogoPresc() { //Utilizar o no el logo del centro educativo en la receta
    if ($("#isStudyCenterPresc").is(':checked')) $(".divStudyCenterPresc").show();
    else $(".divStudyCenterPresc").hide();
  }
</script>