<div class="row">
	<div class="col-md-12">
		<div class="btn-group  pull-right"><a href="index.php?view=medic-categories/new" class="btn btn-default"><i class="fas fa-plus"></i> Agregar Especialidad</a>
		</div>
		<h1>Lista de Especialidades</h1>
		<div class="clearfix"></div>
		<?php

		$categories = CategoryMedicData::getAll();
		if (count($categories) > 0):?>
			<table class="table table-bordered table-hover">
				<thead>
					<th>Nombre</th>
					<th style="width:80px;"></th>
				</thead>
				<?php
				foreach ($categories as $category) : ?>
					<tr>
						<td><?php echo $category->name ?></td>
						<td style="width:80px;" class="td-actions">
							<a href="index.php?view=medic-categories/edit&id=<?php echo $category->id; ?>" rel="tooltip" title="Editar" class="btn btn-simple btn-warning btn-xs"><i class='fas fa-pencil-alt'></i></a>
							<a href="index.php?action=medic-categories/delete&id=<?php echo $category->id; ?>" rel="tooltip" title="Eliminar" onClick='return confirmar()' class=" btn-simple btn btn-danger btn-xs"><i class='far fa-trash-alt'></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			<p class='alert alert-danger'>No hay Especialidades</p>
		<?php endif; ?>
		</table>
	</div>
</div>
</div>
</div>
<script type="text/javascript">
	function confirmar() {
		var flag = confirm("¿Seguro que deseas eliminar la especialidad?");
		if (flag == true) {
			return true;
		} else {
			return false;
		}
	}
</script>