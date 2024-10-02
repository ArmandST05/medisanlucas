<?php
$actualDate = date('Y-m-d');
$startDate = (isset($_GET["sd"])) ? $_GET["sd"] : $actualDate;
$endDate = (isset($_GET["ed"])) ? $_GET["ed"] : $actualDate;
$user = UserData::getLoggedIn();
$userType = $user->user_type;

$searchPaymentTypeId = (isset($_GET["searchPaymentTypeId"])) ? $_GET["searchPaymentTypeId"] : 0;
$paymentTypes = PaymentTypeData::getAll();
$inputs = OperationData::getAllSalesByDates($startDate, $endDate, $searchPaymentTypeId, "all", "all");
$expenses = OperationData::getAllExpensesByDates($startDate, $endDate);
?>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <form>
        <input type="hidden" name="view" value="cashier-balance/index-personal">
        <div class="row">
          <div class="col-md-3">
            <label>Tipo pago</label>
            <select name="searchPaymentTypeId" class="form-control">
              <option value="0">-- TODOS --</option>
              <?php foreach ($paymentTypes as $paymentType) : ?>
                <option value="<?php echo $paymentType->id; ?>" <?php echo ($searchPaymentTypeId == $paymentType->id) ? "selected" : "" ?>><?php echo $paymentType->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label>Fecha inicial</label>
            <input type="date" name="sd" <?php echo ($userType != "su") ? "min='" . date("Y-m-d") . "'" : "" ?> value="<?php echo $startDate ?>" class="form-control">
          </div>
          <?php if ($userType == "su") : ?>
            <div class="col-md-3">
              <label>Fecha final</label>
              <input type="date" name="ed" <?php echo ($userType != "su") ? "min='" . date("Y-m-d") . "'" : "" ?> value="<?php echo $endDate ?>" class="form-control">
            </div>
          <?php endif; ?>
          <div class="col-md-2">
            <br>
            <input type="submit" class="btn btn-success btn-block" value="Procesar">
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-2">
            <a class="btn btn-primary btn-block" target="_blank" href="/?view=reports/box-cut-personal-word&startDate=<?php echo $startDate; ?>&endDate=<?php echo $endDate; ?>&paymentTypeId=<?php echo $searchPaymentTypeId; ?>">Imprimir Corte</a>
          </div>
          <?php if ($userType == "su") : ?>
            <div class="col-md-2">
              <input type="button" class="btn btn-primary btn-block" value="Exportar Excel" id="btnExport" onclick="addLog(0,8,4,'Se descargó el archivo de Cortes del personal')">
            </div>
          <?php endif; ?>
        </div>

      </form>

    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-md-12">
      <div class="clearfix"></div>
      <h1>Cortes del día <?php echo $startDate . " a " . $endDate ?> </h1>

      <h3>Ingresos</h3><br>
      <table id="datosexcel" border='1' class="table table-bordered table-hover">
        <thead>
          <th>Folio</th>
          <th>Médico</th>
          <th>Nombre del paciente</th>
          <th>Comentarios</th>
          <th>Estatus</th>
          <th>Conceptos</th>
          <th>Pagos</th>
          <th>Total venta</th>
        </thead>

        <?php
        $totalRealInputs = 0;
        $totalInputs = 0;
        $typeP = 0;
        $totalMedicines = 0;
        $totalCash = 0;
        $totalCreditCard = 0;
        $totalDebitCard = 0;
        $totalBankDraft = 0;

        foreach ($inputs as $input) {
          $totalInputs += $input->total;
          $medicsString = OperationData::getMedicsBySaleString($input->id);
          $medicName = (isset($medicsString)) ? $medicsString : "Venta mostrador";
          echo "
          <tr class='success'>
          <td>$input->id</td>
          <td>$medicName</td>
          <td>$input->patient_name</td>
          <td>$input->description</td>";
          if ($input->status_id == 1) {
            echo "<td>PAGADA</td>";
          } else {
            echo "<td>PENDIENTE</td>";
          }
          echo "<td>";
          $operationDetails = OperationDetailData::getAllByOperationId($input->id);
          foreach ($operationDetails as $operationDetail) {
            $product = ProductData::getById($operationDetail->product_id);
            echo $operationDetail->quantity . " " . $product->name . "<br>";
            if ($product->type_id == "4") { //Total de medicinas
              $totalMedicines += ($operationDetail->quantity * $operationDetail->price);
            }
          }
          echo "</td>";
          echo "<td>";
          $payments = OperationPaymentData::getAllByOperationId($input->id);
          foreach ($payments as $payment) {
            $totalRealInputs += $payment->total;
            echo $payment->getType()->name . ": $" . number_format($payment->total, 2), "<br>";
            if ($payment->payment_type_id == "1") {
              $totalCash += $payment->total;
            } else if ($payment->payment_type_id == "2") {
              $totalDebitCard += $payment->total;
            } else if ($payment->payment_type_id == "3") {
              $totalCreditCard += $payment->total;
            } else if ($payment->payment_type_id == "6") {
              $totalBankDraft += $payment->total;
            }
          }
          echo "</td>
                <td>$" . number_format($input->total, 2) . "</td>
                </tr>";
        }
        ?>
        <tr>
          <td></td>
          <td><label>TOTAL GENERAL:</label></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td class='success'><label>$<?php echo number_format($totalRealInputs, 2) ?></label></td>
          <td class='success'><label>$<?php echo number_format($totalInputs, 2) ?></label></td>
        </tr>
      </table>

      <h3>Entradas</h3><br>
      <table class="table table-bordered" id='datosexcel' style="width:350px;">
        <thead>
          <th>Forma de pago</th>
          <th>Cantidad</th>
          <th>Total</th>
          <th>Facturado</th>
        </thead>
        <?php
        //Obtiene todos los tipos de pagos y después obtiene todos los pagos realizados de ese tipo para obtener la sumatoria.
        $totalInputs = 0;
        $totalInvoice = 0;
        $selectedPaymentTypes = ($searchPaymentTypeId != 0) ? [PaymentTypeData::getById($searchPaymentTypeId)] : $paymentTypes;

        foreach ($selectedPaymentTypes as $paymentType) :
          $inputs = OperationData::getInputsSales($paymentType->id, $startDate, $endDate);

          $subtotalInputs = 0;
          $subtotalInvoice = 0;
          foreach ($inputs as $input) {
            $totalInputs += $input->total;
            $subtotalInputs += $input->total;
            //Revisar si es facturado.
            if ($input->is_invoice == 1) {
              $subtotalInvoice += $input->total;
              $totalInvoice += $input->total;
            }
          } ?>
          <tr class='success'>
            <td><?php echo $paymentType->name ?></td>
            <td><?php echo count($inputs) ?></td>
            <td><?php echo number_format($subtotalInputs, 2) ?></td>
            <td><?php echo number_format($subtotalInvoice, 2) ?></td>
          </tr>
        <?php
        endforeach;
        echo "<tr><td><label>Total:</label></td><td></td><td class='success'><label>" . number_format($totalInputs, 2) . "</label></td><td class='success'><label>" . number_format($totalInvoice, 2) . "</label></td></tr>";
        ?>
      </table>

      <h3>Gastos</h3><br>
      <table class="table table-bordered table-hover" border='1' id="datosexcel" style="width:750px;">
        <thead>
          <th>Conceptos</th>
          <th>Precio</th>
          <th>Cantidad</th>
          <th>Total</th>
        </thead>
        <?php
        $totalExpenses = 0;
        foreach ($expenses as $expense) {
          $totalExpenses += $expense->price * $expense->quantity;
          $product = ProductData::getById($expense->product_id);
          echo "<tr class='danger'>
              <td>$product->name</td>
              <td>$$expense->price</td>
                      <td>$expense->quantity</td>
                      <td>$" . number_format($expense->price * $expense->quantity, 2) . "</td>
          </tr>";
        }
        ?>
        <tr>
          <td><label>Total:</label></td>
          <td></td>
          <td class='danger'><label>$<?php echo number_format($totalExpenses, 2) ?></label></td>
        </tr>
      </table>

      <h3>Salidas</h3><br>
      <table class="table table-bordered" id='datosexcel' style="width:350px;">
        <thead>
          <th>Forma de pago</th>
          <th>Cantidad</th>
          <th>Total</th>
          <th>Facturado</th>
        </thead>
        <?php
        $totalOutputs = 0;
        $totalInvoiceOutputs = 0;
        $selectedPaymentTypes = ($searchPaymentTypeId != 0) ? [PaymentTypeData::getById($searchPaymentTypeId)] : $paymentTypes;
        foreach ($selectedPaymentTypes as $paymentType) :
          $outputs = OperationData::getOutputsExpenses($paymentType->id, $startDate, $endDate);
          $subtotalOutput = 0;
          $subtotalOInvoice = 0;
          foreach ($outputs as $output) {
            $totalOutputs += $output->total;
            $subtotalOInvoice += $output->total;
            //Revisar si se facturó
            if ($output->is_invoice == 1) {
              $subtotalOInvoice += $output->total;
              $totalInvoiceOutputs += $output->total;
            }
          }
        ?>
          <tr class='danger'>
            <td><?php echo $paymentType->name ?></td>
            <td><?php echo count($outputs) ?></td>
            <td><?php echo number_format($subtotalOutput, 2) ?></td>
            <td><?php echo number_format($subtotalOInvoice, 2) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td><label>Total:</label></td>
          <td></td>
          <td class='danger'><label><?php echo number_format($totalOutputs, 2) ?></label></td>
          <td class='danger'><label><?php echo number_format($totalInvoiceOutputs, 2) ?></label></td>
        </tr>
        <input type="hidden" id="egre" name="egre" value="<?php echo $totalOutputs ?>">
      </table>

      <table id='datosexcel' border='1' class='table table-bordered table-hover'>
        <tr>
          <th>EFECTIVO: $<?php echo number_format($totalCash, 2) ?></th>
          <th>SALIDAS: $<?php echo number_format($totalExpenses, 2) ?></th>
          <th>ENTREGA: $<?php echo number_format($totalCash - $totalExpenses, 2) ?></th>
          <th>MEDICAMENTO: $<?php echo number_format($totalMedicines, 2) ?></th>
          <th>TARJETAS: $<?php echo number_format($totalCreditCard + $totalDebitCard, 2)?></th>
        </tr>
      </table>
    </div>
  </div>

  <script src="assets/jquery.btechco.excelexport.js"></script>
  <script src="assets/jquery.base64.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $("#btnExport").click(function(e) {
        $("#datosexcel").btechco_excelexport({
          containerid: "datosexcel",
          datatype: $datatype.Table,
          filename: 'Cortes Personal'
        });

      });

    });
  </script>