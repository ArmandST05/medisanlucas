<?php
$product = ProductData::getById($_GET["id"]);
if ($product != null) :
?>
  <div class="row">
    <div class="col-md-8">
      <h1><?php echo $product->name ?> <small>Editar Producto/Medicamento</small></h1>
      <?php if (isset($_COOKIE["updatedProduct"])) : ?>
        <p class="alert alert-info">La informacion del producto/medicamento se ha actualizado exitosamente.</p>
      <?php setcookie("updatedProduct", "", time() - 18600);
      endif; ?>
      <br><br>
      <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=products/update" role="form">

        <div class="form-group">
          <label for="inputEmail1" class="col-lg-3 control-label">Código de barras*</label>
          <div class="col-md-8">
            <input type="text" name="barcode" class="form-control" id="barcode" value="<?php echo $product->barcode; ?>" placeholder="Codigo de barras del Producto">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-3 control-label">Nombre*</label>
          <div class="col-md-8">
            <input type="text" name="name" class="form-control" id="name" value="<?php echo $product->name; ?>" placeholder="Nombre del Producto">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-3 control-label">Precio de Entrada*</label>
          <div class="col-md-8">
            <input type="text" name="priceIn" class="form-control" value="<?php echo $product->price_in; ?>" id="priceIn" placeholder="Precio de entrada">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-3 control-label">Precio de Salida*</label>
          <div class="col-md-8">
            <input type="text" name="priceOut" class="form-control" id="priceOut" value="<?php echo $product->price_out; ?>" placeholder="Precio de salida">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-3 control-label">Mínima en inventario:</label>
          <div class="col-md-8">
            <input type="text" name="minimumInventory" class="form-control" value="<?php echo $product->minimum_inventory; ?>" id="inputEmail1" placeholder="Mínimo en Inventario (Predeterminado 10)">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Fracción (Medicamento):</label>
          <div class="col-md-6">
            <input type="text" name="fraction" class="form-control" placeholder="Fracción(Medicamento)" value="<?php echo $product->fraction; ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-3 control-label">Está activo</label>
          <div class="col-md-8">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="isActiveUser" <?php echo ($product->is_active_user) ? "checked": ""?>>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-lg-offset-3 col-lg-8">
            <input type="hidden" name="id" value="<?php echo $product->id; ?>">
            <button type="submit" class="btn btn-success">Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>