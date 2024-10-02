<div class="row">
	<div class="col-md-12">
		<div class="btn-group  pull-right"><a href="index.php?view=income-concepts/new" class="btn btn-default"><i class="fas fa-plus"></i> Agregar Concepto</a>

		</div>
		<h1>Lista de conceptos</h1>
		<div class="clearfix"></div>
		<?php
		$concepts = ProductData::getAllByTypeId(1);
		if (count($concepts) > 0) {
		?>
			<table class="table table-bordered table-hover">
				<thead>
					<th>Nombre</th>
					<th>Descripción</th>
					<th>Precio Salida</th>
					<th>Tipo</th>
					<th style="width:80px;"></th>
				</thead>
				<?php
				foreach ($concepts as $concept) {
				?>
					<tr>
						<td><?php echo $concept->name; ?></td>
						<td><?php echo $concept->description; ?></td>
						<td>$<?php echo number_format($concept->price_out,2); ?></td>
						<td><?php echo $concept->getType()->name; ?></td>
						<td style="width:80px;" class="td-actions">
							<a href="index.php?view=income-concepts/edit&id=<?php echo $concept->id; ?>&name=<?php echo $concept->name; ?>" rel="tooltip" title="Editar" class="btn btn-simple btn-warning btn-xs"><i class='fas fa-pencil-alt'></i></a>
						</td>
					</tr>
				<?php

				}
				?>
			</table>
		<?php
		} else {
			echo "<p class='alert alert-danger'>No hay conceptos</p>";
		}
		?>
		</table>
	</div>
</div>
</div>
</div>
<script type="text/javascript">
	function confirmar() {
		var flag = confirm("¿Deseas eliminar el concepto?");
		if (flag == true) {
			return true;
		} else {
			return false;
		}
	}
</script>