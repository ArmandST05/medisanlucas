  <?php
	$configuration = ConfigurationData::getAll();
	$user = UserData::getLoggedIn();
	$userType = $user->user_type;

	$patient = PatientData::getById($_GET["patientId"]);
	if (!$patient) {
		echo "<script>
				alert('El paciente no existe');
				window.location='index.php?view=patients/index';
			</script>";
	}
	$reservations = ReservationData::getByPatient($_GET["patientId"], 0);
	$totalReservations = count($reservations);
	?>
  <div class="row">
  	<div class="col-md-12">
  		<div class="card">
  			<div class="cafrd-header" data-background-color="blue">
  				<h1>Historial citas <?php echo $patient->name ?></h1>
  			</div>
  			<div class="btn-group pull-right">
  				<a type="button" class="btn btn-sm btn-primary" target="_blank" href="index.php?view=reservations/patient-history-pdf&patientId=<?php echo $patient->id?>"><i class="fas fa-file"></i> Exportar historial PDF</a>
  				<button type="button" class="btn btn-sm btn-default" value="Exportar Excel" id="btnExport"><i class="fas fa-file"></i> Exportar historial Excel</button>
  			</div>
  			<div class="card-content">
  				<?php
					if ($totalReservations > 0) :
						// si hay resultados
					?>
  					<table class="table table-bordered table-hover table-responsive" id="tableHistory">
  						<h5>
  							<?php if ($totalReservations == 1) echo $totalReservations . " Resultado";
								else echo $totalReservations . " Resultados";
								?></h5>
  						<thead>
  							<th>Fecha/Hora</th>
  							<th>Paciente</th>
  							<th>Teléfono</th>
  							<th>Familiar</th>
  							<th>Médico</th>
  							<th>Motivo</th>
  							<th>Estatus cita</th>
							<th>Estatus venta</th>
  							<th>Archivos anexados</th>
  							<th>Receta</th>
  							<th>Acciones</th>
  						</thead>
  						<?php
							foreach ($reservations as $reservation) :
								$medic = $reservation->getMedic();
								$files = PatientData::getAllFilesByPatientReservation($patient->id, $reservation->id);

								if (isset($reservation->sale_id)) { //Existe una venta
									if ($reservation->sale_status_id == 0) { //La venta está pendiente
									  $saleStatus = "PENDIENTE LIQUIDAR"; //Venta no liquidada (Pago pendiente liquidar)
									} else { //La venta está liquidada
									  $saleStatus = "PAGADA"; //Venta liquidada (Pago liquidado)
									}
								  }else{
									$saleStatus = "NO PAGADO";
								  }
							?>
  							<tr>
  								<td><?php echo $reservation->day_name . " " . $reservation->date_at_format; ?></td>
  								<td><?php echo $patient->name; ?></td>
  								<td><?php echo $patient->cellphone; ?></td>
  								<td><?php echo $patient->relative_name; ?></td>
  								<td><?php echo $medic->name; ?></td>
  								<td><?php echo $reservation->reason; ?></td>
  								<td><?php echo $reservation->status_name; ?></td>
								  <td><?php echo $saleStatus; ?></td>
  								<td>
  									<?php foreach ($files as $file) : ?>
  										<a href="storage_data/files/<?php echo $file->path ?>" target="__blank" class="btn btn-default btn-sm"><i class="fas fa-eye"></i><?php echo $file->path ?></a>
  									<?php endforeach; ?>
  								</td>
  								<td>
  									<?php if ($reservation->status_id == 2) : ?>
  										<?php if ($configuration['active_personalized_prescription']->value == 1) : ?>
  											<a href='./?view=reservations/report-prescription-personalized&id=<?php echo $reservation->id ?>' target="__blank" class='btn btn-primary btn-xs'><i class="fas fa-file-medical"></i> Receta Médica</a>
  										<?php else : ?>
  											<a href='./?view=reservations/report-prescription&id=<?php echo $reservation->id ?>' target="__blank" class='btn btn-primary btn-xs'><i class="fas fa-file-medical"></i> Receta Médica</a>
  										<?php endif; ?>
  									<?php endif; ?>
  								</td>
  								<td style="width:240px;">
  									<?php if ($userType == "su") : ?>
  										<?php if ($reservation->status_id == 2) : ?>
  											<a href="index.php?view=reservations/details&id=<?php echo $reservation->id ?>" class="btn btn-default btn-xs"><i class='fas fa-align-justify'></i> Detalles de la cita</a>
  										<?php endif; ?>
  										<a href="index.php?view=reservations/edit-patient&id=<?php echo $reservation->id ?>" class="btn btn-warning btn-xs"><i class='fas fa-pencil-alt'></i> Editar</a>
  										<?php if ($reservation->status_id != 2) : ?>
  											<button id="btnDeleteReservation" onclick="deleteReservation('<?php echo $reservation->id ?>')" class='btn btn-danger btn-xs'><i class="fas fa-trash"></i> Eliminar</button>
  										<?php endif; ?>
  									<?php elseif ($userType == "a") : ?>
  										<a href="index.php?view=reservations/edit-patient&id=<?php echo $reservation->id; ?>" class="btn btn-warning btn-xs">Cita</a>
  									<?php elseif ($userType == "do") : ?>
  										<?php if ($reservation->status_id == 2) : ?>
  											<a href="index.php?view=reservations/details&id=<?php echo $reservation->id ?>" class="btn btn-default btn-xs"><i class='fas fa-align-justify'></i> Detalles de la cita</a>
  										<?php endif; ?>
  										<a href="index.php?view=reservations/edit-patient&id=<?php echo $reservation->id ?>" class="btn btn-warning btn-xs"><i class='fas fa-pencil-alt'></i> Editar</a>
  										<?php if ($reservation->status_id != 2) : ?>
  											<button id="btnDeleteReservation" onclick="deleteReservation('<?php echo $reservation->id ?>')" class='btn btn-danger btn-xs'><i class="fas fa-trash"></i> Eliminar</button>
  										<?php endif; ?>
  									<?php else : ?>
  										<a href="index.php?view=reservations/edit-patient&id=<?php echo $reservation->id; ?>" class="btn btn-warning btn-xs"><i class='fa fa-pencil'></i> Editar</a>
  										<?php if ($reservation->status_id != 2) : ?>
  											<button id="btnDeleteReservation" onclick="deleteReservation('<?php echo $reservation->id ?>')" class='btn btn-danger btn-xs'><i class="fas fa-trash"></i> Eliminar</button>
  										<?php endif; ?>
  									<?php endif; ?>
  								</td>
  							</tr>
  						<?php endforeach; ?>
  					</table>
  				<?php else : echo "<p class='alert alert-danger'>No se encontraron resultados</p>";
					endif; ?>
  			</div>
  		</div>
  	</div>
  </div>
  <script src="assets/jquery.btechco.excelexport.js"></script>
  <script src="assets/jquery.base64.js"></script>
  <script>
  	$(document).ready(function() {

  		$("#btnExport").click(function(e) {
  			$("#tableHistory").btechco_excelexport({
  				containerid: "tableHistory",
  				datatype: $datatype.Table,
  				filename: 'Historial citas ' + '<?php echo $patient->name ?>'
  			});
  		});

  	});

  	function deleteReservation(id) {
  		Swal.fire({
  			title: '¿Estás seguro?',
  			text: "¡No serás capaz de revertir esto!",
  			icon: 'warning',
  			showCancelButton: true,
  			confirmButtonColor: '#3085d6',
  			cancelButtonColor: '#d33',
  			cancelButtonText: 'Cancelar',
  			confirmButtonText: 'Sí, Eliminar'
  		}).then((result) => {
  			if (result.value) {
  				$.ajax({
  					url: "./?action=reservations/delete-reservation", // json datasource
  					type: "POST", // method, by default get
  					data: "id=" + id,
  					success: function() {
  						location.reload();
  					},
  					error: function() { // error handling
  						Swal.fire(
  							'Error',
  							'La cita no se ha podido eliminar.',
  							'error'
  						);
  					}
  				});
  			}
  		})
  	};
  </script>