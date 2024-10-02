<?php
$medicine = MedicineData::getById($_GET["id"]);
if ($medicine != null) :
?>
  <div class="row">
    <div class="col-md-8">
      <h1><?php echo $medicine->generic_name ?> <small>Editar Medicamento</small></h1>
      <br><br>
      <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="index.php?action=medicines/update" role="form">
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Nombre genérico*</label>
          <div class="col-md-6">
            <input type="text" name="genericName" required class="form-control" id="genericName" value="<?php echo $medicine->generic_name; ?>" placeholder="Nombre del medicamento">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Grupo Terapéutico*</label>
          <div class="col-md-6">
            <input type="text" name="therapeuticGroupName" required class="form-control" id="therapeuticGroupName" value="<?php echo $medicine->therapeutic_group_name; ?>" placeholder="Nombre del grupo terapéutico">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Fórmula farmacéutica*</label>
          <div class="col-md-6">
            <input type="text" name="pharmaceuticalForm" required class="form-control" id="pharmaceuticalForm" value="<?php echo $medicine->pharmaceutical_form; ?>" placeholder="Fórmula farmacéutica">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Concentración*</label>
          <div class="col-md-6">
            <input type="text" name="concentration" required class="form-control" id="concentration" value="<?php echo $medicine->concentration; ?>" placeholder="Concentración">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Presentación*</label>
          <div class="col-md-6">
            <input type="text" name="presentation" required class="form-control" id="presentation" value="<?php echo $medicine->presentation; ?>" placeholder="Presentación">
          </div>
        </div>
        <div class="form-group">
          <div class="col-lg-offset-3 col-lg-8">
            <input type="hidden" name="id" value="<?php echo $medicine->id; ?>">
            <button type="submit" class="btn btn-primary">Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>