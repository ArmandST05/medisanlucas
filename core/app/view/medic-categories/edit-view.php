<?php $category = CategoryMedicData::getById($_GET["id"]); ?>
<h1>Editar Especialidad</h1>
<div class="box box-primary">
  <div class="box-header with-border">
  </div>
  <div class="box-body">
    <form class="form-horizontal" method="post" enctype="multipart/form-data" id="update" action="index.php?action=medic-categories/update" role="form">
      <div class="row">
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
          <div class="col-md-8">
            <input type="text" name="name" value="<?php echo $category->name; ?>" class="form-control" id="name" placeholder="Nombre" required>
          </div>
        </div>

        <div class="col-lg-offset-9 col-lg-2">
          <input type="hidden" name="id" value="<?php echo $category->id; ?>">
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>

      </div>
    </form>
  </div>
</div>