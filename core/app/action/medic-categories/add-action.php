<?php
if(count($_POST)>0){
	$category = new CategoryMedicData();
	$category->name = $_POST["name"];

	if(!$category->add()) Core::alert("Ocurrió un error al agregar.");
	print "<script>window.location='index.php?view=medic-categories/index';</script>";
}
?>