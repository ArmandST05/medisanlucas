<?php $category = ExpenseCategoryData::getById($_GET["id"]); ?>
<div class="row">
  <div class="col-md-12">
    <h1>Editar categoría gasto</h1>
    <br>
    <form class="form-horizontal" method="post" enctype="multipart/form-data" autocomplete='off' action="index.php?action=expense-categories/update" role="form">
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" value="<?php echo $category->name; ?>" class="form-control" id="name" placeholder="Nombre">
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <input type="hidden" name="id" value="<?php echo $category->id; ?>">
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </div>
    </form>
  </div>
</div>
</div>
</div>