<?php
$patient = PatientData::getById($_GET["id"]);
$patient->delete();

Core::alert("¡Eliminado exitosamente!");
print "<script>window.location='index.php?view=patients/index';</script>";
?>