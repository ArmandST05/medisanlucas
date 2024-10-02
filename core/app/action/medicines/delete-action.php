<?php
$medicine = MedicineData::getById($_GET["id"]);
$medicine->delete();

Core::redir("./index.php?view=medicines/index");
?>