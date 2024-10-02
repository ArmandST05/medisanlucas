<?php
$paymentTypes = PaymentTypeData::getAll();
$total = 0;
$totalPayment  = 0;
$concepts = ProductData::getAllByTypeId(2);
?>

<div class="row">
  <div class="col-md-12">
    <h1>Gastos</h1>
    <form method="post" action="index.php?action=expenses/add-cart-concept" autocomplete="off">
      <div class="form-group">

        <div class="col-lg-3">
          <label for="inputEmail1" class="col-lg-3 control-label">Conceptos</label>
          <select name="conceptId" id="conceptId" class="form-control" onchange="selectConcept(this.value)" autofocus required>
            <option value="0">-- SELECCIONE --</option>
            <?php foreach ($concepts as $concept) : ?>
              <option value="<?php echo $concept->id; ?>"><?php echo $concept->id . " - " . $concept->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-lg-2">
          <label for="inputEmail1" class="col-lg-3 control-label">Costo</label>
          <input type="number" class="form-control" step="any" autofocus name="cost" id="cost" required placeholder="Costo ...">
        </div>

        <div class="col-lg-2">
          <label for="inputEmail1" class="col-lg-3 control-label">Cantidad</label>
          <input type="number" class="form-control" value="1" min="1" autofocus name="quantity" placeholder="Cantidad" required>
        </div>

        <div class="col-lg-3">
          <label for="inputEmail1">Fecha de caducidad</label>
          <input type="date" name="expirationDate" class="form-control">
        </div>

        <div class="col-lg-2">
          <br>
          <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
    </form>

  </div>
</div>

<?php if (isset($_SESSION["expense"])) : ?>
  <h2>Lista</h2>
  <table class="table table-bordered table-hover">
    <thead>
      <th style="width:30px;">ID</th>
      <th style="width:250px;">Concepto</th>
      <th style="width:250px;">Categoría</th>
      <th style="width:30px;">Cantidad</th>
      <th style="width:30px;">Costo</th>
      <th style="width:30px;">Total</th>
    </thead>
    <?php foreach ($_SESSION["expense"] as $c) :
      $concept = ProductData::getById($c["id"]);
    ?>
      <tr>
        <td><?php echo $c["id"]; ?></td>
        <td><?php echo $concept->name; ?></td>
        <td><?php echo $concept->getExpenseCategory()->name; ?></td>
        <td><?php echo $c["quantity"]; ?></td>
        <!--td><?php echo $c["expirationDate"]; ?></td-->

        <td><b>$ <?php echo number_format($c["cost"], 2); ?></b></td>
        <td><b>$ <?php echo number_format($c["cost"] * $c["quantity"], 2); ?></b></td>
        <?php $productTotal = $c["cost"] * $c["quantity"];
        $total += $productTotal; ?>
        <td style="width:30px;"><a href="index.php?action=expenses/delete-cart-concept&conceptId=<?php echo $concept->id; ?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i> Cancelar</a></td>
      </tr>

    <?php endforeach; ?>
  </table>

  <h2>Resumen</h2>

  <form method="post" action="index.php?action=expenses/add-cart-payment" autocomplete="off">

    <div class="form-group">
      <label for="inputEmail1" class="col-lg-2 control-label">Forma de pago</label>
      <div class="col-lg-4">
        <select name="paymentType" class="form-control" required>
          <option value="">-- SELECCIONE --</option>
          <?php foreach ($paymentTypes as $type) : ?>
            <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-lg-3">
        <input type="number" step="any" name="total" required class="form-control" id="total" placeholder="Total">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary"><i class=""></i> Agregar</button>
      </div>
    </div>
  </form>
  <div class="row">
    <div class="col-md-6">
      <?php if (isset($_SESSION["expensePaymentTypes"])) : ?>
        <table class="table table-bordered table-hover">
          <thead>
            <th>Id</th>
            <th>Forma de pago</th>
            <th>Total</th>
            <th></th>
          </thead>
          <?php foreach ($_SESSION["expensePaymentTypes"] as $t) :
            $tPay = PaymentTypeData::getById($t["id"]);
          ?>
            <tr>
              <td st><?php echo $t["id"]; ?></td>
              <td><?php echo  $tPay->name; ?></td>
              <td><b>$ <?php $tp = $t["total"];
                        $totalPayment += $tp;
                        echo number_format($tp, 2); ?></b></td>
              <td style="width:25px;"><a href="index.php?action=expenses/delete-cart-payment&typePaymentId=<?php echo $tPay->id; ?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a></td>
            </tr>

          <?php endforeach; ?>

        </table>
      <?php endif; ?>

      <?php if ($totalPayment > 0) : ?>
        <div class="col-lg-4 col-md-offset-6">
          <input style="text-align:right;" type="text" id="totalGen1" name="totalGen1" value="<?php echo number_format($totalPayment, 2) ?>" class="form-control">
        </div>
      <?php endif; ?>
    </div>


    <div class="col-md-6">
      <table class="table table-bordered">
        <td>
          <p>Total</p>
        </td>
        <td>
          <p><b>$ <?php echo number_format($total, 2); ?></b></p>
        </td>
        </tr>

      </table>
    </div>
  </div>

  <form method="post" class="form-horizontal" id="formProcess" action="index.php?action=expenses/add">
    <div class="form-group">
      <div class="col-lg-9">
        <textarea class="form-control" name="description" rows="10" cols="50" placeholder="Comentarios"></textarea>
      </div>
      <div class="col-lg-offset-6 col-lg-7">
        <div class="checkbox">
          <label>
            <a href="index.php?action=expenses/delete-cart-all" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Cancelar</a>
            <button class="btn btn-primary"><i class="glyphicon glyphicon-usd"></i><i class="glyphicon glyphicon-usd"></i> Finalizar</button>
          </label>

          <input type="hidden" name="total" value="<?php echo $total; ?>" class="form-control" placeholder="Total">
          <input type="hidden" id="discount" name="discount" value="0" class="form-control">
          <input type="hidden" id="totalGen" name="totalGen" value="<?php echo $totalPayment ?>" class="form-control">
        </div>
      </div>
    </div>
  </form>
  </div>
  </div>
<?php endif; ?>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#conceptId").select2({});
  });

  function selectConcept(valor) {
    $.ajax({
      type: "POST",
      url: "./?action=products/get-price-in-product",
      data: "id=" + valor,

      error: function() {
        alert("Error en consulta.");
      },
      success: function(data) {
        $("#cost").val(data);

      }
    });
  }

  $("#formProcess").submit(function(e) {
    let discount = $("#discount").val();
    let money = $("#totalGen").val();
    if (money > (<?php echo $total; ?> - discount)) {
      alert("No se puede efectuar la operación, verifica tus cantidades");
      e.preventDefault();
    } else {
      if (discount == "") {
        discount = 0;
      }
      if (go) {} else {
        e.preventDefault();
      }
    }
  });
</script>