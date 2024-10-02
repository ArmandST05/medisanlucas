<div class="row">
	<div class="col-md-12">
		<a href="index.php?view=users/new" class="btn btn-default pull-right"><i class="fas fa-plus"></i> Nuevo Usuario</a>
		<h1>Lista de Usuarios</h1>
		<br>
		<?php

		$users = UserData::getAll();
		if (count($users) > 0) {
		?>
			<table class="table table-bordered table-hover">
				<thead>
					<th>Nombre completo</th>
					<th>Nombre de usuario</th>
					<th>Email</th>
					<th>Activo</th>
					<th>Admin</th>
					<th></th>
				</thead>
				<?php
				foreach ($users as $user) {
				?>
					<tr>
						<td><?php echo $user->name . " " . $user->lastname; ?></td>
						<td><?php echo $user->username; ?></td>
						<td><?php echo $user->email; ?></td>
						<td>
							<?php if ($user->is_active) : ?>
								<i class="glyphicon glyphicon-ok"></i>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($user->user_type == "su") : ?>
								<i class="glyphicon glyphicon-ok"></i>
							<?php endif; ?>
						</td>
						<td style="width:30px;"><a href="index.php?view=users/edit&id=<?php echo $user->id; ?>" class="btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i> Editar</a></td>
					</tr>
			<?php

				}
				echo "</table>";
			} else {
				// no hay usuarios
			}


			?>


	</div>
</div>