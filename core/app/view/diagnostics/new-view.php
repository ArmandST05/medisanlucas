<h1>Agregar Diagnóstico</h1>
<div class="box box-primary">
  <div class="box-header with-border">
  </div>
  <div class="box-body">
    <form class="form-horizontal" method="post" enctype="multipart/form-data" id="add" action="index.php?action=diagnostics/add" role="form">
      <div class="row">
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Clave*</label>
          <div class="col-md-8">
            <input type="text" name="code" class="form-control" id="code" placeholder="Clave" required>
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
          <div class="col-md-8">
            <input type="text" name="name" class="form-control" id="name" placeholder="Nombre" required>
          </div>
        </div>

        <div class="col-lg-offset-9 col-lg-2">
          <button type="submit" class="btn btn-primary">Agregar</button>
        </div>

      </div>
    </form>
  </div>
</div>