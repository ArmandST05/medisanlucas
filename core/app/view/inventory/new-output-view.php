<?php
$typePay = PaymentTypeData::getAll();
$total = 0;
$totalPay = 0;
$products = array_merge(ProductData::getAllByTypeId(3), ProductData::getAllByTypeId(4));
?>
<div class="row">
	<div class="col-md-12">
		<h1>Salidas</h1>

		<form method="post" action="index.php?action=inventory/add-output-product" autocomplete="off">
			<div class="row">

				<div class="form-group">

					<div class="col-lg-3">
						<label for="inputEmail1" class="col-lg-3 control-label">Medicamento/Conceptos</label>
						<select name="id" id="productId" class="form-control" autofocus required>
							<option value="0">-- SELECCIONE --</option>
							<?php foreach ($products as $product) : ?>
								<option value="<?php echo $product->id; ?>"><?php echo $product->id . " - " . $product->name ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="col-lg-2">
						<label for="inputEmail1" class="col-lg-3 control-label">Cantidad</label>
						<input type="number" class="form-control" value="1" autofocus name="quantity" placeholder="Cantidad" required>

					</div>

					<div class="col-lg-2">
						<br>
						<button type="submit" class="btn btn-primary">Agregar</button>
					</div>
				</div>
			</div>
		</form>
	</div>

	<?php if (isset($_SESSION["errorsSal"])) : ?>
		<h2>Errores</h2>
		<p></p>
		<table class="table table-bordered table-hover">
			<tr class="danger">
				<th>Codigo</th>
				<th>Producto</th>
				<th>Mensaje</th>
			</tr>
			<?php foreach ($_SESSION["errorsSal"]  as $error) :
				$product = ProductData::getById($error["id"]);
			?>
				<tr class="danger">
					<td><?php echo $product->id; ?></td>
					<td><?php echo $product->name; ?></td>
					<td><b><?php echo $error["message"]; ?></b></td>
				</tr>

			<?php endforeach; ?>
		</table>
	<?php unset($_SESSION["errorsSal"]);
	endif; ?>
	<!--- Carrito de compras :) -->
	<?php if (isset($_SESSION["cartOutputs"])) : ?>
		<h2>Lista de venta</h2>
		<table class="table table-bordered table-hover" style="width:675px;">
			<thead>
				<th style="width:30px;">Id</th>
				<th style="width:250px;">Producto/Concepto</th>
				<th style="width:250px;">Tipo</th>
				<th style="width:30px;">Cantidad</th>

				<th></th>
			</thead>
			<?php foreach ($_SESSION["cartOutputs"] as $cartProduct) :
				$product = ProductData::getById($cartProduct["id"]);
			?>
				<tr>
					<td><?php echo $product->id; ?></td>
					<td><?php echo $product->name; ?></td>
					<td><?php echo $cartProduct["type"]; ?></td>
					<td><?php echo $cartProduct["quantity"]; ?></td>
					<td style="width:30px;"><a href="index.php?action=inventory/delete-output-cart&productId=<?php echo $product->id; ?>" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-remove"></i> Cancelar</a></td>
				</tr>

			<?php endforeach; ?>
		</table>

		<h2>Comentarios/anotaciones</h2>

		<form method="post" class="form-horizontal" action="index.php?action=inventory/add-output">

			<div class="form-group">
				<div class="col-lg-6">
					<textarea class="form-control" name="description" rows="10" cols="50" placeholder="Comentarios"></textarea>
				</div>
				<div class="col-lg-10 pull-right">
					<label>
						<br>
						<a href="index.php?action=inventory/delete-output-cart" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Cancelar</a>
						<button class="btn btn-primary"></i> Dar salida</button>
					</label>

					<input type="hidden" name="total" value="0" class="form-control" placeholder="Total">
					<input type="hidden" id="discount" name="discount" value="0" class="form-control">
					<input type="hidden" id="totalGen" name="totalGen" value="0" class="form-control">

				</div>
			</div>
		</form>
	<?php endif; ?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#productId").select2({});
	});
</script>