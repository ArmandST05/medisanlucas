<?php
/**
* BookMedik
* @author evilnapsis
* @url http://evilnapsis.com/about/
**/

	$user = new ReservationData();
	$user->id_medico = $_POST["id_medico"];
    $user->id_paciente = $_POST["id_paciente"]; 
	$user->textarea = $_POST["note"];
	$id=$_POST["id_reser"];
	$fecha=$_POST["fecha"];
    $res=$_POST["note"];


	$user->add_resumen($res);

//Core::alert("Agregado exitosamente");
print "<script>window.location='index.php?view=detallereservation&id=".$id."&id_paciente=".$_POST["id_paciente"]."&fecha=".$fecha."';</script>";




?>