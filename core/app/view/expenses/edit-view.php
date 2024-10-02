<?php
$conceptDetails = OperationDetailData::getAllByOperationId($_GET["id"]);
$payments = OperationPaymentData::getAllByOperationId($_GET["id"]);
$expense = OperationData::getById($_GET["id"]);

$total = 0;
$totalPay = 0;

$paymentTypes = PaymentTypeData::getAll();
$total = 0;
$totalPay  = 0;
$concepts = ProductData::getAllByTypeId(2);
?>
<div class="row">
  <div class="col-md-12">
    <h1>Editar Gasto</h1>
    <form method="post" action="index.php?action=expenses/add-concept" autocomplete="off">
      <tr>
        <div class="form-group">

          <div class="col-lg-3">
            <label for="inputEmail1" class="col-lg-3 control-label">Medicamento/Conceptos</label>
            <select name="conceptId" id="conceptId" class="form-control" onchange="selectConcept(this.value)" autofocus required>
              <option value="0">-- SELECCIONE --</option>
              <?php foreach ($concepts as $concept) : ?>
                <option value="<?php echo $concept->id; ?>"><?php echo $concept->id . " - " . $concept->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-lg-2">
            <label for="inputEmail1" class="col-lg-3 control-label">Costo</label>
            <input type="number" step="any" class="form-control" autofocus name="cost" id="cost" required placeholder="Costo ...">
          </div>
          <div class="col-lg-2">
            <label for="inputEmail1" class="col-lg-3 control-label">Cantidad</label>
            <input type="number" class="form-control" value="1" autofocus name="quantity" placeholder="Cantidad" required>
          </div>
          <div class="col-lg-3">
            <label for="inputEmail1">Fecha de caducidad</label>
            <input type="date" name="expirationDate" class="form-control">
          </div>
          <div class="col-lg-2">
            <br>
            <button type="submit" class="btn btn-primary">Agregar</button>
            <input type="hidden" class="form-control" value="<?php echo $_GET["id"] ?>" autofocus name="expenseId">
          </div>
    </form>
  </div>
</div>

<h2>Lista</h2>
<table class="table table-bordered table-hover">
  <thead>
    <th style="width:30px;">ID</th>
    <th style="width:250px;">Concepto</th>
    <th style="width:250px;">Categoría</th>
    <th style="width:30px;">Costo</th>
    <th style="width:30px;">Cantidad</th>
    <th style="width:30px;">Total</th>
    <th></th>
  </thead>
  <?php foreach ($conceptDetails as $c) :
    $concept = ProductData::getById($c->product_id);
  ?>
    <tr>
      <td><?php echo $c->product_id; ?></td>
      <td><?php echo $concept->name; ?></td>
      <td><?php echo $concept->getExpenseCategory()->name; ?></td>
      <td><b>$ <?php echo number_format($c->price, 2); ?></b></td>
      <td><?php echo $c->quantity; ?></td>
      <td><b>$ <?php echo number_format($c->price * $c->quantity, 2); ?></b></td>
      <?php $totalProduct = $c->price * $c->quantity;
      $total += $totalProduct; ?>
      <td style="width:30px;"><a href="index.php?action=expenses/delete-concept&expenseId=<?php echo $_GET['id']; ?>&conceptId=<?php echo $c->id; ?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i> Cancelar</a></td>
    </tr>
  <?php endforeach; ?>
</table>

<h2>Resumen</h2>

<form method="post" action="index.php?action=expenses/add-payment" autocomplete="off">

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Forma de pago</label>
    <div class="col-lg-3">
      <select name="paymentTypeId" class="form-control" required>
        <option value="">-- SELECCIONE --</option>
        <?php foreach ($paymentTypes as $type) : ?>
          <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-lg-2">
      <input type="text" name="total" required class="form-control" id="total" placeholder="Total">
    </div>
    <div class="col-lg-2">
      <input type="date" name="date" required class="form-control" id="date" value="<?php echo date("Y-m-d") ?>">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary"><i class=""></i> Agregar</button>
    </div>
  </div>
  <input type="hidden" id="expenseId" name="expenseId" value="<?php echo $_GET["id"] ?>" class="form-control" autocomplete='off'>
</form>
<div class="row">
  <div class="col-md-6">
    <table class="table table-bordered table-hover">
      <thead>
        <th>ID</th>
        <th>Forma de pago</th>
        <th>Total</th>
        <th></th>
      </thead>
      <tbody>
        <?php foreach ($payments as $t) :
          $tPay = PaymentTypeData::getById($t->payment_type_id);
        ?>
          <tr>
            <td><?php echo $t->payment_type_id; ?></td>
            <td><?php echo  $tPay->name; ?></td>
            <td><b>$ <?php $tp = $t->total;
                      $totalPay += $tp;
                      echo number_format($tp, 2); ?></b></td>
            <td style="width:25px;"><a href="index.php?action=expenses/delete-payment&expenseId=<?php echo $_GET["id"] . "&paymentId=" . $t->id; ?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a></td>

          </tr>

        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if ($totalPay > 0) : ?>
      <div class="col-lg-4 col-md-offset-6">
        <input style="text-align:right;" type="text" id="totalGen1" name="totalGen1" value="<?php echo number_format($totalPay, 2) ?>" class="form-control">
      <?php endif; ?>
      </div>
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

<form method="post" class="form-horizontal" id="processbuyPay" action="index.php?action=expenses/update">
  <div class="form-group">
    <div class="col-lg-6">
      <textarea class="form-control" name="description" rows="10" cols="50" placeholder="Comentarios"><?php echo $expense->description ?></textarea>
    </div>
    <div class="col-lg-offset-6 col-lg-10">
      <div class="checkbox">
        <label>
          <button class="btn btn-primary"><i class="glyphicon glyphicon-usd"></i> Finalizar</button>
        </label>
        <input type="hidden" name="total" value="<?php echo $total; ?>" class="form-control" placeholder="Total">
        <input type="hidden" id="totalGen" name="totalGen" value="<?php echo $totalPay ?>" class="form-control">
        <input type="hidden" id="discount" name="discount" value="0" class="form-control">
      </div>
      <input type="hidden" id="expenseId" name="expenseId" value="<?php echo $_GET["id"] ?>" class="form-control" autocomplete='off'>
    </div>
  </div>
</form>
<script>
  $("#processbuyPay").submit(function(e) {
    let money = parseFloat($("#totalGen").val());
    let total = parseFloat($("#total").val());
    
    if (money > total) {
      alert("No se puede efectuar la operación, verifica tus cantidades");
      e.preventDefault();
    } else {
      if (go) {} else {
        e.preventDefault();
      }
    }
  });
  $(document).ready(function() {
    $("#conceptId").select2({});
  });

  function selectConcept(value) {
    $.ajax({
      type: "POST",
      url: "./?action=products/get-price-in",
      data: "id=" + value,

      error: function() {
        alert("Error al consultar el precio del producto");
      },
      success: function(data) {
        $("#cost").val(data);

      }
    });
  }
</script>

</div>