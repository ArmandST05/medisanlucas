<?php
$category = CategoryMedicData::getById($_GET["id"]);

if($category->delete()) Core::alert("¡Eliminado exitosamente!");
else Core::alert("Ocurrió un error al eliminar.");
Core::redir("./index.php?view=medic-categories/index");
?>