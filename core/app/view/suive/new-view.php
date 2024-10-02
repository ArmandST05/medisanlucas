<?php
$states = StateData::getAll();
$counties = CountyData::getAll();
$institutions = SuiveFormatData::getAllInstitutions();
?>
<div class="row">
  <div class="col-md-12">
    <h1>SUIVE-1</h1>
    <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=suive/add" role="form" autocomplete="off">
      <div class="form-group">
        <div class="col-lg-3">
          <label for="inputEmail1" class="control-label">Unidad</label>
          <input type="text" class="form-control" value="" name="unity" required>
        </div>
        <div class="col-lg-3">
          <label for="inputEmail1" class="control-label">Clave Unidad Suave</label>
          <input type="text" class="form-control" value="" name="suaveUnityCode" required>
        </div>
        <div class="col-lg-3">
          <label for="inputEmail1" class="control-label">Semana No.</label>
          <input id="date" type="number" step="1" min="1" class="form-control" value="" name="weekNumber" required>
        </div>
        <div class="col-lg-3">
          <label for="inputEmail1" class="control-label">Fecha de Inicio</label>
          <input id="date" type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" name="startDate" required>
        </div>
        <div class="col-lg-3">
          <label for="inputEmail1" class="control-label">Fecha de Fin</label>
          <input id="date" type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" name="endDate" required>
        </div>
        <div class="col-lg-3">
          <label for="inputEmail1" class="control-label">CLUES</label>
          <input type="text" class="form-control" value="" name="clues" required>
        </div>

        <div class="col-lg-3">
          <label for="inputEmail1" class="control-label">Localidad</label>
          <input type="text" class="form-control" value="" name="communityName" required>
        </div>

        <div class="col-lg-3">
          <label for="inputEmail1" class="col-lg-3 control-label">Municipio</label>
          <select name="countyId" id="countyId" class="form-control" id="combobox" required>
            <option value="">-- SELECCIONE --</option>
            <?php foreach ($counties as $county) : ?>
              <option value="<?php echo $county->id; ?>"><?php echo $county->catalog_key . " - " . $county->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-3">
          <label for="inputEmail1" class="control-label">Jurisdicción</label>
          <input type="text" class="form-control" value="" name="jurisdictionName" required>
        </div>
        <div class="col-lg-3">
          <label for="inputEmail1" class="col-lg-3 control-label">Estado</label>
          <select name="stateId" id="stateId" class="form-control" id="combobox" required>
            <option value="">-- SELECCIONE --</option>
            <?php foreach ($states as $state) : ?>
              <option value="<?php echo $state->id; ?>"><?php echo $state->catalog_key . " - " . $state->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-3">
          <label for="inputEmail1" class="col-lg-3 control-label">Institución</label>
          <select name="institutionId" id="institutionId" class="form-control" id="combobox" required onchange="selectInstitution()">
            <option value="">-- Institución --</option>
            <?php foreach ($institutions as $institution) : ?>
              <option value="<?php echo $institution->id; ?>"><?php echo $institution->id . " - " . $institution->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-lg-3" id="divInstitutionName">
          <label for="inputEmail1" class="control-label">Otra Institución</label>
          <input type="text" class="form-control" value="" name="institutionName">
        </div>

        <div class="col-lg-3" id="divInstitutionName">
          <label for="inputEmail1" class="control-label">Archivo
          <input type="file" class="form-control" name="file">
        </div>

        <div class="col-lg-3">
          <br>
          <button type="submit" class="btn btn-primary">Capturar</button>
        </div>
      </div>
  </div>

  </form>
</div>
</div>

</div>
<!--/.content-->
</div>
<!--/.span9-->
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#countyId").select2({});
    $("#stateId").select2({});
    $("#divInstitutionName").hide();
  });

  function selectInstitution() {
    if ($("#institutionId").val() == 4) $("#divInstitutionName").show();
    else $("#divInstitutionName").hide();
  }
</script>