<?php
$product = ProductData::getById($_GET["id"]);
$product->deactivate();

Core::redir("./index.php?view=products/index");
?>