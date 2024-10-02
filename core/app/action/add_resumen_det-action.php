<?php
/**
* BookMedik
* @author evilnapsis
* @url http://evilnapsis.com/about/
**/

	$user = new ReservationData();
	$user->id_medico = $_POST["id_medico"];
    $user->id_paciente = $_POST["id_paciente"]; 
	$user->textarea = $_POST["textarea"];
	$user->add_resumen();

//Core::alert("Agregado exitosamente!");
print "<script>window.location='index.php?view=patients';</script>";




?>