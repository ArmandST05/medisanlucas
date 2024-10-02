<?php
$paymentTypes = PaymentTypeData::getAll();

$details = OperationDetailData::getAllByOperationId($_GET["id"]);

$saleReservations = [];
foreach ($details as $detail) {
  $saleReservations[$detail->reservation_id][] = $detail;
}

$saleReservationsKeys = array_keys($saleReservations);

$paymentDetails = OperationPaymentData::getAllByOperationId($_GET["id"]);
$sale = OperationData::getById($_GET["id"]);
$patient = PatientData::getById($sale->patient_id);
$products = array_merge(ProductData::getAllByTypeId(3), ProductData::getAllByTypeId(4), ProductData::getAllByTypeId(1)); //Insumos,Medicamentos y conceptos ingresos para venta
$reservationsWithoutSale = ReservationData::getWithoutSaleByPatient($sale->patient_id); //Citas que no han sido cobradas
$subtotalSale = 0;
$total = 0;
$totalPayment = 0;
$discountPercentage = $sale->discount_percentage;
$discountTotal = 0;

?>
<div class="row">
  <div class="col-md-12">
    <div class="row">
      <h1>Ingresos</h1>
    </div>
    <div class="row">
      <div class="form-group">
        <div class="col-lg-3">
          <label class="control-label">Cliente:</label>
          <input type="text" class="form-control" autofocus name="cliente" id="cliente" placeholder="Cliente" value="<?php echo $patient->name ?>" readonly>
        </div>
        <form method="post" action="index.php?action=sales/add-reservations" autocomplete="off">
          <div class="col-lg-5">
            <label class="control-label">Agregar nuevas citas a cobrar:</label><br>
            <select name="collectReservations[]" id="collectReservations" class="form-control" required>
              <?php foreach ($reservationsWithoutSale as $reservationWs) :
                $totals = ReservationData::getTotalsByReservation($reservationWs->id);
              ?>
                <option value="<?php echo $reservationWs->id; ?>" data-total-products="<?php echo $totals->total_products ?>"><?php echo $reservationWs->day_name . " " . $reservationWs->date_at_format . "| SERVICIOS: " . $totals->quantity_products . "| TOTAL: $" . number_format($totals->total_products, 2) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-lg-2">
            <label class="control-label">Total estimado:</label><br>
            <input type="number" class="form-control" id="totalNewCollectReservations" value="0" readonly>
          </div>
          <div class="col-lg-1">
            <br>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <input type="hidden" name="saleId" value="<?php echo $sale->id ?>" class="form-control">
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <form method="post" action="index.php?action=sales/add-product" autocomplete="off">
        <div class="form-group">
          <div class="col-lg-3">
            <label for="inputEmail1" class="col-lg-3 control-label">Medicamento/Conceptos</label>
            <select name="productId" id="productId" class="form-control" onchange="selectProduct(this.value)" autofocus required>
              <option value="0">-- SELECCIONE --</option>
              <?php foreach ($products as $product) : ?>
                <option value="<?php echo $product->id; ?>"><?php echo $product->id . " - " . $product->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-lg-3">
            <label for="inputEmail1" class="col-lg-3 control-label">Cita</label>
            <select name="reservationId" id="reservationId" class="form-control" autofocus required>
              <option value="0">-- NO APLICA --</option>
              <?php foreach ($saleReservationsKeys as $reservationId) :
                $reservationData = ReservationData::getById($reservationId);
                if ($reservationData) :
              ?>
                  <option value="<?php echo $reservationData->id; ?>"><?php echo $reservationData->day_name . " - " . $reservationData->date_format ?></option>
              <?php endif;
              endforeach; ?>
            </select>
          </div>
          <div class="col-lg-2">
            <label for="inputEmail1" class="col-lg-3 control-label">Costo</label>
            <input type="text" class="form-control" autofocus name="price" id="price" required placeholder="Costo ...">
          </div>
          <div class="col-lg-2">
            <label for="inputEmail1" class="col-lg-3 control-label">Cantidad</label>
            <input type="number" class="form-control" value="1" autofocus name="quantity" placeholder="Cantidad" required>
          </div>
          <div class="col-lg-2">
            <br>
            <button type="submit" class="btn btn-primary">Agregar</button>
          </div>
          <input type="hidden" id="saleId" name="saleId" value="<?php echo $_GET["id"] ?>" class="form-control" autocomplete='off'>
        </div>
      </form>
    </div>
    <div class="row">
      <?php if (isset($_SESSION["editSaleErrors"])) : ?>
        <h3>Errores</h3>
        <p></p>
        <table class="table table-bordered table-hover">
          <tr class="danger">
            <th>Código</th>
            <th>Producto</th>
            <th>Mensaje</th>
          </tr>
          <?php foreach ($_SESSION["editSaleErrors"]  as $error) :
            $product = ProductData::getById($error["id"]);
          ?>
            <tr class="danger">
              <td><?php echo $product->id; ?></td>
              <td><?php echo $product->name; ?></td>
              <td><b><?php echo $error["message"]; ?></b></td>
            </tr>

          <?php endforeach; ?>
        </table>
      <?php
        unset($_SESSION["editSaleErrors"]);
      endif; ?>
    </div>
    <br>
    <?php foreach ($saleReservations as $indexReservation => $details) :
      $reservation = ReservationData::getById($indexReservation);
      $subtotal = 0;
    ?>
      <div class="row">
        <h4><?php echo ($reservation) ? "Cita " . $reservation->date_format : "" ?></h4>
        <table class="table table-bordered table-hover">
          <thead>
            <th style="width:30px;">ID</th>
            <th style="width:250px;">Concepto</th>
            <th style="width:30px;">Costo</th>
            <th style="width:30px;">Cantidad</th>
            <th style="width:30px;">Total</th>
            <th></th>
          </thead>
          <tbody>
            <?php foreach ($details as $detail) :
              $concept = ProductData::getById($detail->product_id);
            ?>
              <tr>
                <td><?php echo $detail->product_id; ?></td>
                <td><?php echo $concept->name; ?></td>
                <td><b>$<?php echo number_format($detail->price, 2); ?></b></td>
                <td><?php echo $detail->quantity; ?></td>
                <td><b>$<?php echo number_format($detail->price * $detail->quantity, 2); ?></b></td>
                <?php $totalProduct = $detail->price * $detail->quantity;
                $subtotal += $totalProduct;
                $subtotalSale += $totalProduct;
                ?>
                <td style="width:30px;"><a href="index.php?action=sales/delete-product&saleId=<?php echo $_GET["id"] . "&detailId=" . $detail->id; ?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i> Cancelar</a></td>
              </tr>
            <?php endforeach; ?>
            <tr>
              <td colspan="4" class="text-right"><b>Subtotal</b></td>
              <td><b>$<?php echo number_format($subtotal, 2); ?></b></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php endforeach;
    $discountTotal = (($subtotalSale * $discountPercentage) / 100); ?>

    <h2>Resumen</h2>
    <form method="post" action="index.php?action=sales/add-payment" autocomplete="off">
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Forma de pago</label>
        <div class="col-lg-3">
          <select name="paymentType" class="form-control" required>
            <option value="">-- SELECCIONE --</option>
            <?php foreach ($paymentTypes as $paymentType) : ?>
              <option value="<?php echo $paymentType->id; ?>"><?php echo $paymentType->name; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-2">
          <input type="number" name="total" required class="form-control" id="total" placeholder="Total" required>
        </div>
        <div class="col-lg-2">
          <input type="date" name="date" required class="form-control" id="date" placeholder="Fecha" required>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary"><i class=""></i> Agregar</button>
        </div>
      </div>
      <input type="hidden" id="saleId" name="saleId" value="<?php echo $_GET["id"] ?>" class="form-control" autocomplete='off'>
      <input type="hidden" name="totalSale" value="<?php echo ($subtotalSale - $discountTotal); ?>" class="form-control" placeholder="Total">
      <input type="hidden" name="totalPayment" value="<?php echo $totalPayment ?>" class="form-control">
    </form>
    <br>
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
            <?php foreach ($paymentDetails as $paymentDetail) :
              $paymentData = PaymentTypeData::getById($paymentDetail->payment_type_id);
            ?>
              <tr>
                <td><?php echo $paymentDetail->id; ?></td>
                <td><?php echo  $paymentData->name; ?></td>
                <td><b>$ <?php $tp = $paymentDetail->total;
                          $totalPayment += $tp;
                          echo number_format($tp, 2); ?></b></td>
                <td style="width:25px;"><a href="index.php?action=sales/delete-payment&saleId=<?php echo $_GET["id"] . "&paymentId=" . $paymentDetail->id . "&totalSale=" . ($subtotalSale - $discountTotal) . "&totalPayment=" . $totalPayment; ?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php if ($totalPayment > 0) : ?>
          <div class="col-lg-4 col-md-offset-6">
            <input style="text-align:right;" type="text" value="<?php echo number_format($totalPayment, 2) ?>" class="form-control" readonly>
          <?php endif; ?>
          </div>
      </div>
      <div class="col-md-6">
        <table class="table table-bordered">
          <tr>
            <td>
              <p><b>Subtotal</b></p>
            </td>
            <td>
              <p><b>$ <?php echo number_format($subtotalSale, 2); ?></b></p>
            </td>
          </tr>
          <tr>
            <td><b>Descuento (%)<b></td>
            <td>
              <form method="POST" action="index.php?action=sales/add-discount" autocomplete="off">
                <div class="col-md-6">
                  <input type="number" id="discountSale" name="discountSale" class="form-control" value="<?php echo $discountPercentage ?>" min="0" max="100">
                  <input type="hidden" name="saleId" value="<?php echo $_GET["id"] ?>" class="form-control" autocomplete='off'>
                </div>
                <div class="col-md-6">
                  <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
              </form>
            </td>
          </tr>
          <tr>
            <td>
              <p><b>Total</b></p>
            </td>
            <td>
              <p><b>$ <?php echo number_format(($subtotalSale - $discountTotal), 2); ?></b></p>
            </td>
          </tr>
          <tr>
            <td><b>Saldo</b></td>
            <td><b>$<?php echo ((floatval(($subtotalSale - $discountTotal) - $totalPayment)) < 0) ? "0.00" : number_format(($subtotalSale - $discountTotal) - $totalPayment, 2); ?></b></td>
          </tr>
        </table>
      </div>
    </div>

    <form method="post" class="form-horizontal" id="formUpdateSale" action="index.php?action=sales/update">
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <div class="checkbox">
            <label>
              <input name="is_oficial" type="hidden" value="1">
            </label>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-10">
          <textarea class="form-control" name="description" rows="10" cols="50" placeholder="Comentarios"><?php echo $sale->description ?></textarea>
        </div>
        <div class="col-lg-offset-8 col-lg-10">
          <div class="checkbox">
            <label>
              <button class="btn btn-primary"><i class="glyphicon glyphicon-usd"></i> Finalizar</button>
            </label>
            <input type="hidden" id="totalSale" name="totalSale" value="<?php echo $subtotalSale ?>" class="form-control" placeholder="Total">
            <input type="hidden" id="totalPayment" name="totalPayment" value="<?php echo $totalPayment ?>" class="form-control">
            <input type="hidden" id="discount" name="discount" value="<?php echo $discountTotal ?>" class="form-control">
            <input type="hidden" id="discountPercentage" name="discountPercentage" value="<?php echo $discountPercentage ?>" class="form-control">
            <input type="hidden" id="saleId" name="saleId" value="<?php echo $_GET["id"] ?>" class="form-control" autocomplete='off'>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
  $(document).ready(function() {
    $("#productId").select2({});
    $("#collectReservations").select2({
      multiple: true,
    });
    $("#collectReservations").val(null).trigger("change");

    $('#collectReservations').on('select2:select', function(e) {
      calculateTotalNewReservations();
    });
    $('#collectReservations').on('select2:unselect', function(e) {
      calculateTotalNewReservations();
    });
  });

  $("#formUpdateSale").submit(function(e) {
    totalPayment = $("#totalPayment").val();
    discount = $("#discount").val();
    if (totalPayment > (<?php echo $subtotalSale; ?> - discount)) {
      alert("No se puede efectuar la operacion verifica tus cantidades");
      e.preventDefault();
    } else {
      if (discount == "") {
        discount = 0;
      }
      /************Validar si se liquidó *************/
      if (totalPayment >= (<?php echo $subtotalSale; ?> - discount)) {
        go = confirm("Cambio: $" + (totalPayment - (<?php echo $subtotalSale; ?> - discount)) + " Pesos");
      } else {
        go = confirm("Pendiente por pagar: $" + ((<?php echo $subtotalSale; ?> - discount) - totalPayment) + " Pesos");
      }
      if (go) {} else {
        e.preventDefault();
      }
    }
  });

  function selectProduct(id) {
    $.ajax({
      type: "POST",
      url: "./?action=products/get-price-in",
      data: "id=" + id,

      error: function() {
        alert("Error al consultar el precio del concepto.");
      },
      success: function(data) {
        $("#price").val(data);
      }
    });
  }

  function calculateTotalNewReservations() {

    //Calcula el total a pagar por los productos(conceptos/ingresos) seleccionados
    let totalProducts = 0;
    let selectedNewReservations = $('#collectReservations').select2('data');

    $(selectedNewReservations).each(function(index, reservation) {
      let value = reservation.element.attributes['data-total-products'].nodeValue;
      totalProducts += (isNaN(parseFloat(value))) ? 0 : parseFloat(value);
    });

    $("#totalNewCollectReservations").val(parseFloat(totalProducts).toFixed(2));
  }
</script>