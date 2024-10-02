<?php
if(count($_POST)>0){

	$diagnostic = DiagnosticData::getById($_POST["id"]);
	$diagnostic->code = $_POST["code"];
	$diagnostic->name = $_POST["name"];

	if(!$diagnostic->update()) Core::alert("Ocurrió un error al actualizar.");
	print "<script>window.location='index.php?view=diagnostics/index';</script>";
}
?>