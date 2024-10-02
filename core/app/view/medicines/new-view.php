<div class="row">
  <div class="col-md-12">
    <h1>Nuevo Medicamento</h1>
    <div id="result"></div>
    <br>
    <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="index.php?action=medicines/add" role="form" autocomplete="off">
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre genérico*</label>
        <div class="col-md-6">
          <input type="text" name="genericName" required class="form-control" id="genericName" placeholder="Nombre del medicamento">
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Grupo Terapéutico*</label>
        <div class="col-md-6">
          <input type="text" name="therapeuticGroupName" required class="form-control" id="therapeuticGroupName" placeholder="Nombre del grupo terapéutico">
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Fórmula farmacéutica*</label>
        <div class="col-md-6">
          <input type="text" name="pharmaceuticalForm" required class="form-control" id="pharmaceuticalForm" placeholder="Fórmula farmacéutica">
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Concentración*</label>
        <div class="col-md-6">
          <input type="text" name="concentration" required class="form-control" id="concentration" placeholder="Concentración">
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Presentación*</label>
        <div class="col-md-6">
          <input type="text" name="presentation" required class="form-control" id="presentation" placeholder="Presentación">
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
      </div>
  </div>
  </form>
</div>
</div>
</div>
<script>
  $(document).ready(function() {
  
  });
</script>