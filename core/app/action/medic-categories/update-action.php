<?php
if(count($_POST)>0){
	$category = CategoryMedicData::getById($_POST["id"]);
	$category->name = $_POST["name"];
	
	if(!$category->update()) Core::alert("Ocurrió un error al actualizar.");
	print "<script>window.location='index.php?view=medic-categories/index';</script>";
}
?>