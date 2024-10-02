<?php
$medicine = MedicineData::getById($_GET["id"]);
$medicine->deactivate();

Core::redir("./index.php?view=medicines/index");
?>