<?php
$concept = ProductData::getById($_GET["id"]);
$categories = ExpenseCategoryData::getAll();
?>
<div class="row">
  <div class="col-md-12">
    <h1>Editar Concepto</h1>
    <br>
    <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=expense-concepts/update" role="form">

      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" class="form-control" id="name" value='<?php echo $concept->name; ?>' autofocus placeholder="Nombre">
        </div>
      </div>

      <div class="form-group">
        <!-- LOS MEDICAMENTOS SIEMPRE SE COLOCAN COMO GASTOS DE PROVEEDORES-->
        <?php if ($concept->expense_category_id == 1) : ?>
          <input type="hidden" name="category" class="form-control" id="category" value='<?php echo $concept->expense_category_id ?>' autofocus>
          <?php else : ?>
          <label for="inputEmail1" class="col-lg-2 control-label">Categoría gastos</label>
          <div class="col-md-6">
            <select name="category" class="form-control">
              <option value="">-- SELECCIONE --</option>
              <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category->id; ?>" <?php echo ($concept->expense_category_id == $category->id) ? "selected" : "" ?>><?php echo $category->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>
      </div>
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <input type="hidden" name="id" value="<?php echo $_GET["id"] ?>">
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </div>
    </form>
  </div>
</div>
</div>
</div>