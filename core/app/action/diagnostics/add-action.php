<?php
if(count($_POST)>0){
	$diagnostic = new DiagnosticData();
	$diagnostic->code = $_POST["code"];
	$diagnostic->name = $_POST["name"];
	if(!$diagnostic->add()) Core::alert("Ocurrió un error al agregar.");
	print "<script>window.location='index.php?view=diagnostics/index';</script>";
}
?>