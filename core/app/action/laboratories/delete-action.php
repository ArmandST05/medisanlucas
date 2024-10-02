<?php
$laboratory = LaboratoryData::getById($_GET["id"]);

if($laboratory->delete()) Core::alert("¡Eliminado exitosamente!");
else Core::alert("Ocurrió un error al eliminar.");
Core::redir("./index.php?view=laboratories/index");
?>