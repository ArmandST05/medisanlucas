<?php
if(count($_POST)>0){
	$laboratory = new LaboratoryData();
	$laboratory->name = $_POST["name"];

	if(!$laboratory->add()) Core::alert("Ocurrió un error al agregar.");
	print "<script>window.location='index.php?view=laboratories/index';</script>";
}
?>