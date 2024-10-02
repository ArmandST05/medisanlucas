<?php
/**
* BookMedik
* @author evilnapsis
* @url http://evilnapsis.com/about/
**/

	$user = new ReservationData();
	$user->id_reser = $_POST["id_reser"];
	$id=$_POST["id_reser"];
    $res=$_POST["note"];



	$user->upt_resumen($res);

//Core::alert("Actualizado exitosamente");
print "<script>window.close();</script>";




?>