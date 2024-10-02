  <?php $diagnostic = DiagnosticData::getById($_GET["id"]); ?>
  <h1>Editar Diagnóstico</h1>
  <div class="box box-primary">
    <div class="box-body">
      <form class="form-horizontal" method="post" enctype="multipart/form-data" id="update" action="index.php?action=diagnostics/update" role="form">
        <div class="row">
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Clave*</label>
            <div class="col-md-8">
              <input type="text" name="code" value="<?php echo $diagnostic->code; ?>" class="form-control" id="name" placeholder="Clave" required>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
            <div class="col-md-8">
              <input type="text" name="name" value="<?php echo $diagnostic->name; ?>" class="form-control" id="name" placeholder="Nombre" required>
            </div>
          </div>

          <div class="col-lg-offset-9 col-lg-2">
            <input type="hidden" name="id" value="<?php echo $diagnostic->id; ?>">
            <button type="submit" class="btn btn-primary">Actualizar</button>
          </div>

        </div>
      </form>
    </div>
  </div>