<?php
$outputDetails = OperationPaymentData::getAllByOperationId($_GET["id"]);
$description = OperationData::getDescription($_GET["id"]);
$products = array_merge(ProductData::getAllByTypeId(3), ProductData::getAllByTypeId(4));
?>
<div class="row">
  <div class="col-md-12">
    <h1>Editar salida</h1>
    <div class="row">
      <form method="post" action="index.php?action=inventory/update-output-product" autocomplete="off">
        <div class="row">
          <div class="form-group">
            <div class="col-lg-3">
              <label for="inputEmail1" class="col-lg-3 control-label">Medicamento/Conceptos</label>
              <select name="productId" id="productId" class="form-control" autofocus required>
                <option value="0">-- SELECCIONE --</option>
                <?php foreach ($products as $product) : ?>
                  <option value="<?php echo $product->id; ?>"><?php echo $product->id . " - " . $product->name ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-lg-2">
              <label for="inputEmail1" class="col-lg-3 control-label">Cantidad</label>
              <input type="number" class="form-control" value="1" autofocus name="quantity" placeholder="Cantidad" required>
              <input type="hidden" id="operationId" name="operationId" value="<?php echo $_GET["id"] ?>" class="form-control" autocomplete='off'>
            </div>
            <div class="col-lg-2">
              <br>
              <button type="submit" class="btn btn-primary">Agregar</button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <h2>Lista</h2>
    <table class="table table-bordered table-hover">
      <thead>
        <th style="width:30px;">Id</th>
        <th style="width:250px;">Concepto</th>
        <th style="width:250px;">Categoría</th>
        <th style="width:30px;">Cantidad</th>

        <th></th>
      </thead>
      <?php foreach ($outputDetails as $detail) :
        $product = ProductData::getById($detail->product_id);
      ?>
        <tr>
          <td><?php echo $detail->product_id; ?></td>
          <td><?php echo $product->name; ?></td>
          <td><?php echo $product->getType()->name; ?></td>
          <td><?php echo $detail->quantity; ?></td>
          <td style="width:30px;"><a href="index.php?action=inventory/delete-output-product&operationDetailId=<?php echo $detail->id . "&operationId=" . $_GET['id']; ?>" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-remove"></i> Cancelar</a></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <form method="post" class="form-horizontal" action="index.php?action=inventory/update-output-description">
      <div class="form-group">
        <div class="col-lg-6">
          <textarea class="form-control" name="description" rows="10" cols="50" placeholder="Comentarios"><?php echo $description->description ?></textarea>
        </div>
        <div class="col-lg-offset-4 col-lg-10">
          <div class="checkbox">
            <label>
              <button class="btn btn-primary"> Actualizar</button>
            </label>
          </div>
          <input type="hidden" id="operationId" name="operationId" value="<?php echo $_GET["id"] ?>" class="form-control" autocomplete='off'>

        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#productId").select2({});
  });
</script>