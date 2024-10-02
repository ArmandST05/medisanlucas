<?php
/**
* BookMedik
* @author evilnapsis
* @url http://evilnapsis.com/about/
**/

	$user = ReservationData::getById($_POST["id"]);
	$user->id = $_POST["id"];
	$user->datos = $_POST["datos"];
	$user->tel = $_POST["tel"];
	$user->update_asis();

    $user1 = PatientData::getById($_POST["user_id"]);
	$user1->id = $_POST["user_id"];
	$user1->calle = $_POST["calle"];
	$user1->num = $_POST["num"];
	$user1->col = str_replace('"',"'",$_POST["col"]);
	$user1->tel = $_POST["tel"];
	$user1->tel2 = $_POST["tel2"];
	$user1->email = $_POST["email"];
	$user1->fecha_na = $_POST["formfecha"];
	$user1->ref = $_POST["ref"];
	$user1->edad = $_POST["edad"];
    $user1->updateAssistant($_POST["col"]);
    

    //Core::alert("actualizado exitosamente!");
    print "<script>window.location='index.php?view=home';</script>";
 
?>