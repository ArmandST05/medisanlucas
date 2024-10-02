<?php

if(count($_POST)>0){
	$product = ProductData::getById($_POST["id"]);

	$product->name = $_POST["name"];
    $product->minimum_inventory = $_POST["minimumInventory"];
	$product->user_id = $_SESSION["user_id"];
	$product->updateSupply();
	print "<script>window.location='index.php?view=supplies/index';</script>";
}
?>