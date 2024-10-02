<?php
$actualDate = date('Y-m-d');
$startDate = (isset($_GET["sd"])) ? $_GET["sd"] : $actualDate;
$endDate = (isset($_GET["ed"])) ? $_GET["ed"] : $actualDate;

$user = UserData::getLoggedIn();
$userType = $user->user_type;

$paymentTypes = PaymentTypeData::getAll();
$sales = OperationData::getAllSelledProductsByDate($startDate, $endDate);
$expenses = OperationData::getAllExpensesByDates($startDate, $endDate);
?>
<script src="assets/jquery.btechco.excelexport.js"></script>
<script src="assets/jquery.base64.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#btnExport").click(function(e) {
			$("#datosexcel").btechco_excelexport({
				containerid: "datosexcel",
				datatype: $datatype.Table,
				filename: 'Corte'
			});
		});
	});
</script>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<h1>Cortes del día <?php echo $startDate ?> </h1>
			<form>
				<input type="hidden" name="view" value="cashier-balance/index">
				<div class="row">
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
						<input type="submit" class="btn btn-primary btn-block" value="Exportar" id="btnExport">
					</div>
				</div>
			</form>

		</div>
	</div>
	<br>

	<div class="row">

		<div class="col-md-12">
			<div class="clearfix"></div>
			<h2 id="uti" name="uti"> </h2>
			<h3>Ingresos</h3><br>
			<table class="table table-bordered table-hover" id='datosexcel' style="width:750px;">
				<thead>
					<th>Conceptos</th>
					<th>Cantidad</th>
					<th>Total</th>
					<th>Resumen</th>
				</thead>

				<?php
				$totalCSales = 0; //Total venta de conceptos
				foreach ($sales as $sale) {

					$detailsByMedic = OperationData::getAllMedicSalesByProductDate($sale->product_id, $startDate);
					$product = ProductData::getById($sale->product_id);

					$productType = $product->type_id;
					if ($productType <> 4) {
						//OBTENER INGRESOS POR CONCEPTOS MOSTRANDO EL DETALLE DE LAS PERSONAS QUE LAS VENDIERON.
						$totalCSales += $sale->price;

						echo "
							<tr class='success'>
							<td>$product->name</td>
							<td>$sale->quantity</td>
							<td>" . number_format($sale->price, 2) . "</td>
							<td>
						";
						foreach ($detailsByMedic as $detail) {
							$medic = $detail->getMedic(); //Datos del médico
							$medicName = ($medic) ? $medic->name:"Venta de mostrador";
							echo "$medicName <label>Cantidad: </label> $detail->quantity <label>Total: </label> " . number_format($detail->price, 2) . "<br>";
						}
						echo "</td></tr>";
					}
				}

				//MOSTRAR INGRESOS SÓLO DE LOS MEDICAMENTOS MOSTRANDO DETALLE DE LOS MEDICAMENTOS VENDIDOS.
				$quantityM = 0;
				$totalMSales = 0; //Total de venta de medicamentos
				$det = OperationData::getAllProductSalesByDate($startDate);
				foreach ($det as $k) {
					$quantityM += $k->quantity;
					$totalMSales += $k->total;
				}
				echo "
						<tr class='success'>
						<td>MEDICAMENTOS</td>
						<td>" . $quantityM . "</td>
						<td>" . number_format($totalMSales, 2) . "</td>
						<td>
						";
				foreach ($det as $k) {
					$pro = ProductData::getById($k->product_id);
					echo "$pro->name <label>Can: </label> $k->quantity <label> Precio: </label> " . number_format($k->price, 2) . "<label> Total: </label> " . number_format($k->total, 2) . "<br>";
				}

				echo "</td></tr>";

				echo "<tr><td><label>Total:</label></td><td></td><td class='success'><label>" . number_format($totalCSales + $totalMSales, 2) . "</label></td></tr>";
				?>
				<input type="hidden" id="ingre" name="ingre" value="<?php echo $totalCSales + $totalMSales ?>">
			</table>

			<h3>Entradas</h3><br>
			<table class="table table-bordered" id='datosexcel' style="width:350px;">
				<thead>
					<th>Forma de pago</th>
					<th>Total</th>
					<th>Facturado</th>
				</thead>
				<?php
				//Obtiene todos los tipos de pagos y después obtiene todos los pagos realizados de ese tipo para obtener la sumatoria.
				$paymentTypes = PaymentTypeData::getAll();
				$totalInputs = 0;
				$totalInvoice = 0;
				foreach ($paymentTypes as $paymentType) {
				    $inputs = OperationData::getInputsSales($paymentType->id, $startDate, $endDate);
					echo "
					<tr class='success'>
					<td>$paymentType->name</td>";
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
					<td><?php echo number_format($subtotalInputs, 2) ?></td>
					<td><?php echo number_format($subtotalInvoice, 2) ?></td>
					</tr>
				<?php
				}
				echo "<tr><td><label>Total:</label></td><td class='success'><label>" . number_format($totalInputs, 2) . "</label></td><td class='success'><label>" . number_format($totalInvoice, 2) . "</label></td></tr>";
				?>
			</table>


			<h3>Gastos</h3><br>
			<table class="table table-bordered table-hover" id='datosexcel' style="width:750px;">
				<thead>
					<th>Conceptos</th>
					<th>Cantidad</th>
					<th>Total</th>

				</thead>

				<?php
				$totalExpenses = 0;
				foreach ($expenses as $expenseConcept) {
					$totalExpenses += $expenseConcept->price * $expenseConcept->quantity;
					$product = ProductData::getById($expenseConcept->product_id);
					echo "
						<tr class='danger'>
						<td>$product->name</td>
						<td>$expenseConcept->quantity</td>
						<td>" . number_format($expenseConcept->price * $expenseConcept->quantity, 2) . "</td>
						";
					echo "</tr>";
				}
				echo "<tr ><td><label>Total:</label></td><td></td><td class='danger'><label>" . number_format($totalExpenses, 2) . "</label></td></tr>";
				?>


			</table>

			<h3>Salidas</h3><br>
			<table class="table table-bordered" id='datosexcel' style="width:350px;">
				<thead>
					<th>Forma de pago</th>
					<th>Total</th>
					<th>Facturado</th>
				</thead>
				<?php
				$paymentTypes = PaymentTypeData::getAll();
				$totalOutputs = 0;
				$totalInvoiceOutputs = 0;
				foreach ($paymentTypes as $paymentType) {
					$outputs = OperationData::getOutputs($paymentType->id, $startDate);
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
					<tr class='success'>
						<td><?php echo $paymentType->name ?></td>
						<td><?php echo number_format($subtotalOutput, 2) ?></td>
						<td><?php echo number_format($subtotalOInvoice, 2) ?></td>
					</tr>
				<?php }
				echo "<tr><td><label>Total:</label></td><td class='success'><label>" . number_format($totalOutputs, 2) . "</label></td><td class='success'><label>" . number_format($totalInvoiceOutputs, 2) . "</label></td></tr>";
				?>

				<input type="hidden" id="egre" name="egre" value="<?php echo $totalOutputs ?>">
			</table>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			var tot = (parseFloat($('#ingre').val()) - parseFloat($('#egre').val()));
			var tot2 = tot.toFixed(2);

			$('#uti').html("Utilidad: " + tot2);
		});
	</script>