<?php
$medics = MedicData::getAll();
?>
<div class="row">
	<div class="col-md-12">
		<div class="btn-group  pull-right">
			<a href="index.php?view=medics/new" class="btn btn-default"><i class="fas fa-user-md"></i><i class="fas fa-plus"></i> Agregar Médico</a>

		</div>
		<script type="text/javascript">
			function confirmar() {
				var flag = confirm("¿Seguro que deseas eliminar el médico?");
				if (flag == true) {
					return true;
				} else {
					return false;
				}
			}
		</script>
		<h1>Lista de Médicos</h1>
		<div class="clearfix"></div>

		<?php
		if (count($medics) > 0) {
		?>
			<table class="table table-bordered table-hover">
				<thead>
					<th></th>
					<th>Nombre completo</th>
					<th>Área</th>
					<th></th>
				</thead>
				<?php foreach ($medics as $medic):?>
					<tr>
						<td style="background-color:<?php echo $medic->calendar_color ?>; width:2px;"></td>
						<td><?php echo $medic->name ?></td>
						<td><?php if ($medic->category_id != null) {
								echo $medic->getCategory()->name;
							} ?></td>
						<td style="width:180px;">
							<a href="index.php?view=medics/edit&id=<?php echo $medic->id; ?>" class="btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i> Editar</a>
							<?php /*echo "<a href='index.php?view=medics/delete&id=$medic->id' class='btn btn-danger btn-xs onClick='return confirmar()'>Eliminar</a>";*/ ?>
						</td>
					</tr>
					<?php
					?>

			<?php endforeach;
			} else {
				echo "<p class='alert alert-danger'>No hay médicos</p>";
			}
			?>
			</table>
	</div>
</div>
</div>
</div>