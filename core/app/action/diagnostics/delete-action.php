<?php
   	$diagnostic = DiagnosticData::getById($_GET["id"]);
    if($diagnostic->delete()) Core::alert("¡Eliminado exitosamente!");
    else Core::alert("Ocurrió un error al eliminar.");
    print "<script>window.location='index.php?view=diagnostics/index';</script>";
?>