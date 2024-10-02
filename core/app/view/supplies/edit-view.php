<?php
$product = ProductData::getById($_GET["id"]);
if ($product != null) :
?>
  <div class="row">
    <div class="col-md-8">
      <h1><?php echo $product->name ?> <small>Editar insumo</small></h1>
      <?php if (isset($_COOKIE["prdupd"])) : ?>
        <p class="alert alert-info">La informacion del producto se ha actualizado exitosamente.</p>
      <?php setcookie("prdupd", "", time() - 18600);
      endif; ?>
      <br><br>
      <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=supplies/update" role="form">


        <div class="form-group">
          <label for="inputEmail1" class="col-lg-3 control-label">Nombre:*</label>
          <div class="col-md-8">
            <input type="text" name="name" class="form-control" id="name" value="<?php echo $product->name; ?>" placeholder="Nombre del Producto">
          </div>
        </div>

        <div class="form-group">
          <label for="inputEmail1" class="col-lg-3 control-label">Mínimo en inventario:</label>
          <div class="col-md-8">
            <input type="number" step=".01" min="0" name="minimumInventory" class="form-control" value="<?php echo $product->minimum_inventory; ?>" id="inputEmail1" placeholder="Mínimo en Inventario (Default 10)">
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