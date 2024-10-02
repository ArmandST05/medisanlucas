<div class="row">
	<div class="col-md-12">
		<div class="btn-group  pull-right"><a href="index.php?view=laboratories/new" class="btn btn-default"><i class="fas fa-plus"></i> Agregar Laboratorio/Consultorio</a>
		</div>
		<h1>Lista de Laboratorios/Consultorios</h1>
		<div class="clearfix"></div>
		<?php

		$laboratories = LaboratoryData::getAll();
		if (count($laboratories) > 0) : ?>
			<table class="table table-bordered table-hover">
				<thead>
					<th>Nombre</th>
					<th>Estatus</th>
					<th style="width:80px;"></th>
				</thead>
				<?php
				foreach ($laboratories as $laboratory) : ?>
					<tr>
						<td><?php echo $laboratory->name ?></td>
						<td><?php echo ($laboratory->is_active) ? "ACTIVO" : "INACTIVO" ?></td>
						<td style="width:80px;" class="td-actions">
							<a href="index.php?view=laboratories/edit&id=<?php echo $laboratory->id; ?>" rel="tooltip" title="Editar" class="btn btn-simple btn-warning btn-xs"><i class='fas fa-pencil-alt'></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php else : ?>
			<p class='alert alert-danger'>No hay Laboratorios/Consultorios</p>
		<?php endif; ?>
		</table>
	</div>
</div>
</div>
</div>