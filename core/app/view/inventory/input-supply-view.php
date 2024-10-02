<?php
$product = ProductData::getById($_GET["id"]);
?>
<div class="row">
  <div class="col-md-12">
    <h1>Entradas</h1>
    <form method="post" action="index.php?action=inventory/add-input" autocomplete="off">

      <div class="col-lg-3">
        <label for="inputEmail1" class="col-lg-3 control-label">Insumo</label>
        <input class="form-control" id="product" name="product" value="<?php echo $product->name; ?>" readonly>
      </div>

      <div class="col-lg-2">
        <label for="inputEmail1" class="col-lg-3 control-label">Cantidad</label>
        <input type="number" min="1" class="form-control" autofocus name="quantity" id="quantity" placeholder="Cantidad" required>
      </div>

      <div class="col-lg-3">
        <label for="inputEmail1">Lote</label>
        <input type="text" name="lot" class="form-control" required>
      </div>

      <div class="col-lg-2">
        <label for="inputEmail1">Fecha de caducidad</label>
        <input type="date" name="expirationDate" class="form-control" required>
      </div>

      <div class="form-group">
        <input type="hidden" name="id" value="<?php echo $product->id; ?>" required>
        <div class="col-lg-2">
          <br>
          <button type="submit" onClick="return confirmInput()" class="btn btn-primary">Guardar</button>
        </div>
    </form>

  </div>
</div>

</div>
</div>

</div>
<script type="text/javascript">
  function confirmInput() {
    var product = $("#product").val();
    var quantity = $("#quantity").val();
    var flag = confirm("¿Seguro que deseas dar entrada del indumo " + quantity + " " + product + "?");
    if (flag == true) {
      return true;
    } else {
      return false;
    }
  }
</script>
