<?php
if(count($_POST)>0){
	$product = ProductData::getById($_POST["id"]);

	$product->barcode = trim($_POST["barcode"]);
	$product->name = trim($_POST["name"]);
	$product->price_in = $_POST["priceIn"];
	$product->price_out = $_POST["priceOut"];
	$product->fraction = $_POST["fraction"];
  	$product->minimum_inventory = (($_POST["minimumInventory"] != "") ? $_POST["minimumInventory"]:0);

  	$product->is_active_user = ((isset($_POST["isActiveUser"])) ? 1 : 0);
	$product->user_id = $_SESSION["user_id"];
	$product->update();

	setcookie("updatedProduct","true");
	print "<script>window.location='index.php?view=products/edit&id=".$_POST['id']."';</script>";
}
?>