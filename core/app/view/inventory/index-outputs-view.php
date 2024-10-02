<div class="row">
	<div class="col-md-12">
		<div class="btn-group pull-right">
			<a href="index.php?view=inventory/new-output" class="btn btn-default">Nueva salida</a>
		</div>
		<h1><i class='glyphicon glyphicon-shopping-cart'></i> Salidas de medicamentos/insumos</h1>
		<div class="clearfix"></div>
		<?php
		$outputs = OperationData::getAllByTypeId(2);
		if (count($outputs) > 0) {
		?>
			<br>
			<table class="table table-bordered table-hover" id="dataTable">
				<thead>
					<th></th>
					<th>Folio</th>
					<th>Cantidad Insumos</th>
					<th>Fecha</th>
					<th></th>
				</thead>
				<?php foreach ($outputs as $output) : ?>
					<tr>
						<td style="width:30px;"><a href="index.php?view=inventory/edit-output&id=<?php echo $output->id; ?>" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-eye-open"></i></a></td>
						<?php
						echo "<td>" . $output->id . "</td><td>";
						$operations = OperationDetailData::getAllProductsByOperationId($output->id);
						echo count($operations);
						?>
						</td>
						<td><?php echo $output->created_at; ?></td>
						<td><?php echo $output->description; ?></td>
						<!--td style="width:30px;"><a href="index.php?action=sales/delete-output&id=<?php echo $output->id; ?>" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a></td-->
					</tr>
				<?php endforeach; ?>

			</table>
		<?php
		} else {
		?>
			<div class="jumbotron">
				<h2>No hay datos</h2>
				<p>No se ha realizado ninguna operación.</p>
			</div>
		<?php
		}
		?>
	</div>
</div>
<script>
	$(document).ready(function() {
		var dataTable = $('#dataTable').DataTable({
			pageLength: 50,
			ordering: false,
			language: {
				url: 'plugins/datatables/languages/es-mx.json'
			}
		});
	});
</script>