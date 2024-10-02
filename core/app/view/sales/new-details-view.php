<?php
$patientId = (isset($_GET["patientId"]))  ? $_GET['patientId'] : "";
$medicId = (isset($_GET["medicId"]))  ? $_GET['medicId'] : "";
$reservationId = (isset($_GET["reservationId"]))  ? $_GET['reservationId'] : "0";

$patient = PatientData::getById($patientId);
$paymentTypes = PaymentTypeData::getAll();
$products = array_merge(ProductData::getAllByTypeId(3), ProductData::getAllByTypeId(4), ProductData::getAllByTypeId(1)); //Insumos, Medicamentos y conceptos ingresos para venta
$pendingSales = OperationData::getBySaleStatusPatient(0, $patientId);
$reservationsWithoutSale = ReservationData::getWithoutSaleByPatient($patientId); //Citas que no han sido cobradas
$date = (isset($_GET["date"]))  ? $_GET['date'] : date("Y-m-d H:i:s");

$subtotalSale = 0;
$totalPayment = 0;
$discountPercentage = (isset($_SESSION["cart-discount"])) ? floatval($_SESSION["cart-discount"]) : 0;
$discountTotal = 0;

//Si se especificó una cita, agregarla al carrito la primera vez 
if ($reservationId && !isset($_SESSION["cart"])) {
  $_SESSION["cart"][$reservationId] = [];

  $productsNewReservation = ReservationData::getProductsByReservation($reservationId);
  foreach ($productsNewReservation as $product) {
    $productData = ProductData::getById($product->product_id);
    $typeId = $productData->type_id;
    $typeName = $productData->getType()->name;

    //VALIDAR EXISTENCIAS (Medicamentos/Insumos)
    if ($typeId == 3 || $typeId == 4) {
      $stock = OperationDetailData::getStockByProduct($_POST["productId"]);

      //Si hay stock añadimos, si no registramos error
      if (floatval(1) > $stock) {
        $isStock = false;
        $error = array("id" => $product->product_id, "message" => "No hay suficiente cantidad de producto en inventario.");
        $errors[count($errors)] = $error;
        $_SESSION["errors"] = $errors;
      } else $isStock = true;
    } else $isStock = true; //Conceptos ingresos

    if ($isStock == true) {
      $subtotalSale += floatval($productData->price_out); //Agregar a total a mostrar
      $product = array("id" => $product->product_id, "quantity" => 1, "price" => $productData->price_out, "typeId" => $typeId, "typeName" => $typeName);
      $_SESSION["cart"][$reservationId][] = $product;
    }
  }
}

$cart = (isset($_SESSION["cart"])) ? $_SESSION["cart"] : []; //Cargar carrito

if ($cart) {
  $saleReservationsKeys = array_keys($cart);
} else {
  $saleReservationsKeys = null;
}


?>
<div class="row">
  <div class="col-md-12">
    <div class="row">
      <h1>Nueva Venta</h1>
      <div class="form-group">
        <?php if (!empty($pendingSales)) { ?>
          <p class="alert alert-warning">

          <?php
          foreach ($pendingSales as $pendingSale) {
            echo "Pendiente por pagar, " . "<a href='./?view=sales/edit&id=$pendingSale->id'>LIQUIDAR</a><br>";
          }
        }
          ?></p>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="form-group">
    <div class="col-lg-3">
      <label class="control-label">Cliente:</label>
      <input type="text" class="form-control" autofocus name="cliente" id="cliente" placeholder="Cliente" value="<?php echo $patient->name ?>" readonly>
    </div>
    <form method="post" action="index.php?action=sales/add-cart-reservations" autocomplete="off">
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
        <input type="hidden" name="patientId" value="<?php echo $patientId ?>" class="form-control">
        <input type="hidden" name="date" value="<?php echo $date ?>" class="form-control">
      </div>
    </form>
  </div>
</div>
<br>
<form method="post" action="index.php?action=sales/add-cart-product" autocomplete="off">
  <div class="row">
    <div class="form-group">
      <div class="col-lg-4">
        <label for="inputEmail1" class="col-lg-3 control-label">Medicamento/Conceptos:</label>
        <select name="productId" id="productId" class="form-control" onchange="selectProduct(this.value)" autofocus required>
          <option value="0">-- SELECCIONE --</option>
          <?php foreach ($products as $p) : ?>
            <option value="<?php echo $p->id; ?>"><?php echo $p->name . " | " . $p->barcode ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-lg-3">
        <label for="inputEmail1" class="col-lg-3 control-label">Cita</label>
        <select name="reservationId" id="reservationId" class="form-control" required>
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
        <label class="control-label">Costo:</label>
        <div class="input-group">
          <span class="input-group-addon">$</span>
          <input type="number" step="any" class="form-control" autofocus name="price" id="price" required placeholder="Costo">
        </div>
      </div>
      <div class="col-lg-2">
        <label for="inputEmail1" class="col-lg-3 control-label">Cantidad:</label>
        <input type="number" class="form-control" value="1" autofocus name="quantity" placeholder="Cantidad" required>
      </div>
      <div class="col-lg-1">
        <br>
        <button type="submit" class="btn btn-primary">Agregar</button>
      </div>
      <input type="hidden" name="patientId" value="<?php echo $patientId ?>" class="form-control">
      <input type="hidden" name="medicId" value="<?php echo $medicId ?>" class="form-control">
      <input type="hidden" name="date" value="<?php echo $date ?>" class="form-control">
    </div>
  </div>
</form>
<div class="row">
  <div class="col-md-12">
    <?php if (isset($_SESSION["errors"])) : ?>
      <h3>Errores</h3>
      <p></p>
      <table class="table table-bordered table-hover">
        <tr class="danger">
          <th>Código</th>
          <th>Producto</th>
          <th>Mensaje</th>
        </tr>
        <?php foreach ($_SESSION["errors"]  as $error) :
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
      unset($_SESSION["errors"]);
    endif; ?>

    <!--- Carrito de compras -->
    <?php if (isset($_SESSION["cart"])) : ?>
      <h3>Lista de venta</h3>
      <?php foreach ($_SESSION["cart"] as $indexReservation => $cartReservation) :
        $reservation = ReservationData::getById($indexReservation);
      ?>
        <h4><?php echo ($reservation) ? "Cita " . $reservation->date_format : "" ?></h4>
        <table class="table table-bordered table-hover">
          <thead>
            <th style="width:30px;">Id</th>
            <th style="width:250px;">Producto/Concepto</th>
            <th style="width:250px;">Tipo</th>
            <th style="width:30px;">Cantidad</th>
            <th style="width:85px;">Precio Unitario</th>
            <th style="width:100px;">Total</th>
            <th></th>
          </thead>
          <tbody>
            <?php if ($cartReservation && count($cartReservation) > 0) :
              $subtotal = 0;
              foreach ($cartReservation as $productCart) :
                $product = ProductData::getById($productCart["id"]);
            ?>
                <tr>
                  <td><?php echo $product->id; ?></td>
                  <td><?php echo $product->name; ?></td>
                  <td><?php echo $productCart["typeName"]; ?></td>
                  <td><?php echo $productCart["quantity"]; ?></td>
                  <td><b>$<?php echo number_format($productCart["price"], 2); ?></b></td>
                  <td><b>$<?php $totalProduct = $productCart["price"] * $productCart["quantity"];
                          $subtotal += $totalProduct;
                          $subtotalSale += $totalProduct;
                          echo number_format($totalProduct, 2); ?>
                    </b>
                  </td>
                  <td style="width:30px;">
                    <a href="index.php?action=sales/delete-cart-product&reservationId=<?php echo $indexReservation ?>&productId=<?php echo $product->id; ?>&patientId=<?php echo $patientId; ?>&date=<?php echo $date; ?>" class="btn btn-sm btn-danger">
                      <i class="glyphicon glyphicon-remove"></i> Cancelar
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr>
                <td colspan="5" class="text-right"><b>Subtotal</b></td>
                <td><b>$<?php echo number_format($subtotal, 2); ?></b></td>
                <td></td>
              </tr>
            <?php else : ?>
              <tr>
                <td colspan="6">No se han agregado productos</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      <?php endforeach; ?>
      <hr>

      <h3>Pagos</h3>
      <form method="POST" action="index.php?action=sales/add-cart-payment" autocomplete="off">
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Forma de pago:</label>
          <div class="col-lg-4">
            <select name="paymentType" class="form-control" required>
              <option value="">-- SELECCIONE --</option>
              <?php foreach ($paymentTypes as $paymentType) : ?>
                <option value="<?php echo $paymentType->id; ?>"><?php echo $paymentType->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-lg-3">
            <div class="input-group">
              <span class="input-group-addon">$</span>
              <input type="number" name="quantity" step="any" required class="form-control" id="quantity" placeholder="Total">
            </div>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa plus"></i> Agregar pago</button>
          </div>
        </div>
        <input type="hidden" id="patientId" name="patientId" value="<?php echo $patientId ?>" class="form-control">
        <input type="hidden" id="date" name="date" value="<?php echo $date ?>" class="form-control">
        <input type="hidden" id="totalSale" name="totalSale" value="<?php echo $subtotalSale ?>" class="form-control">
      </form>
      <div class="row">
        <div class="col-md-6">
          <table class="table table-bordered">

            <?php if (isset($_SESSION["payments"])) : ?>
              <table class="table table-bordered table-hover">
                <thead>
                  <th>Id</th>
                  <th>Forma de pago</th>
                  <th>Total</th>
                  <th></th>
                </thead>
                <?php foreach ($_SESSION["payments"] as $payment) :
                  $paymentData = PaymentTypeData::getById($payment["id"]);
                ?>
                  <tr>
                    <td><?php echo $payment["id"]; ?></td>
                    <td><?php echo  $paymentData->name; ?></td>
                    <td><b>$ <?php $quantity = $payment["quantity"];
                              $totalPayment += $quantity;
                              echo number_format($quantity, 2); ?></b></td>
                    <td style="width:25px;"><a href="index.php?action=sales/delete-cart-payment&reservationId=<?php echo $reservationId ?>&paymentTypeId=<?php echo $paymentData->id; ?>&patientId=<?php echo $patientId; ?>&medicId=<?php echo $medicId ?>&date=<?php echo $date ?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a></td>
                  </tr>

                <?php endforeach; ?>

              </table>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
          <table class="table table-bordered">
            <tr>
              <td><b>Subtotal venta</b></td>
              <td><b>$ <?php echo number_format($subtotalSale, 2); ?></b></td>
            </tr>
            <tr>
              <td><b>Descuento (%)<b></td>
              <td>
                <form method="POST" action="index.php?action=sales/add-cart-discount" autocomplete="off">
                  <div class="col-md-6">
                    <input type="number" id="discountSale" name="discountSale" class="form-control" value="<?php echo $discountPercentage ?>" min="0" max="100">
                    <input type="hidden" id="patientId" name="patientId" value="<?php echo $patientId ?>" class="form-control">
                    <input type="hidden" id="date" name="date" value="<?php echo $date ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                  </div>
                </form>
              </td>
            </tr>
            <tr>
              <td><b>Pagado<b></td>
              <td><b>$<?php echo number_format($totalPayment, 2) ?></b></td>
            </tr>
            <tr>
              <td><b>Total venta<b></td>
              <td><b>$<?php
                      if ($discountPercentage > 0) {
                        $discountTotal = ($subtotalSale * $discountPercentage) / 100;
                        $total = $subtotalSale - $discountTotal;
                      } else {
                        $discountTotal = 0;
                        $total = $subtotalSale;
                      }
                      echo number_format($total, 2) ?></b></td>
            </tr>
            <tr>
              <td><b>Saldo</b></td>
              <td><b>$<?php echo ((floatval($total - $totalPayment)) < 0) ? "0.00" : number_format($total - $totalPayment, 2); ?></b></td>
            </tr>
          </table>
        </div>
      </div>

      <form method="POST" class="form-horizontal" id="saveSale" action="index.php?action=sales/add">
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
            <textarea class="form-control" name="description" rows="10" cols="50" placeholder="Comentarios"></textarea>
          </div>
          <div class="col-lg-offset-8 col-lg-10">
            <div class="checkbox">
              <label>
                <a href="index.php?action=sales/delete-all-cart&patientId=<?php echo $patientId ?>&date=<?php echo $date ?>" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Cancelar venta</a>
                <button class="btn btn-primary"><i class="glyphicon glyphicon-usd"></i> Finalizar venta</button>
              </label>

              <input type="hidden" name="total" value="<?php echo $total; ?>" class="form-control" placeholder="Total">
              <input type="hidden" id="patientId" name="patientId" value="<?php echo $patientId ?>" class="form-control">
              <input type="hidden" id="discountPercentage" name="discountPercentage" value="<?php echo $discountPercentage ?>" class="form-control">
              <input type="hidden" id="discount" name="discount" value="<?php echo $discountTotal ?>" class="form-control">
              <input type="hidden" id="totalPayment" name="totalPayment" value="<?php echo $totalPayment ?>" class="form-control">
              <input type="hidden" id="date" name="date" value="<?php echo $date ?>" class="form-control">
            </div>
          </div>
        </div>
      </form>
      <script type="text/javascript">
        $("#saveSale").submit(function(e) {
          discount = $("#discount").val();
          money = $("#total").val();
          totalPayment = $("#totalPayment").val();
          if (money > (<?php echo $total; ?> - discount)) {
            alert("No se puede efectuar la operacion verifica tus cantidades");
            e.preventDefault();
          } else {
            if (discount == "") discount = 0;
            //Validar si se liquida
            if (totalPayment >= (<?php echo $total; ?>)) {
              go = confirm("Cambio: $" + (totalPayment - (<?php echo $total; ?>)) + " Pesos");
            } else {
              go = confirm("Pendiente por pagar: $" + ((<?php echo $total; ?>) - totalPayment) + " Pesos");

            }

            if (go) {} else {
              e.preventDefault();
            }
          }
        });
      </script>

    <?php endif; ?>
  </div>
</div>
</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#productId").select2({});
    $('#productId').select2('open');
    $("#collectReservations").select2({
      multiple: true,
    });
    $("#collectReservations").val(null).trigger("change");
  });

  function selectProduct(value) {
    $.ajax({
      type: "POST",
      url: "./?action=products/get-product-price",
      data: "valor=" + value,

      error: function() {
        alert("Ocurrió un error al obtener el precio.");
      },
      success: function(data) {
        $("#price").val(data);
      }
    });
  }

  $('#collectReservations').on('select2:select', function(e) {
    calculateTotalNewReservations();
  });
  $('#collectReservations').on('select2:unselect', function(e) {
    calculateTotalNewReservations();
  });


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