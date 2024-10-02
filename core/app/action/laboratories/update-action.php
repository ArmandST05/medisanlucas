<?php
if(count($_POST)>0){
	$laboratory = LaboratoryData::getById($_POST["id"]);
	$laboratory->name = $_POST["name"];
	$laboratory->is_active = (isset($_POST["isActive"])) ? $_POST["isActive"]:0;
	
	if(!$laboratory->update()) Core::alert("Ocurrió un error al actualizar.");
	print "<script>window.location='index.php?view=laboratories/index';</script>";
}
?>