<?php
if(count($_POST)>0){
  $product = new ProductData();
   $product->name = $_POST["name"];
   $product->minimum_inventory=$_POST["minimumInventory"];
   $product->user_id = $_SESSION["user_id"];

   $addedProduct= $product->addSupply();

  if($_POST["initialInventory"] != "" || $_POST["initialInventory"] != "0"){
    $date = date("d-m-Y (H:i:s)");
    $operation = new OperationDetailData();
    $operation->product_id = $addedProduct[1];
    $operation->operation_type_id = 1;
    $operation->operation_id = 0;
    $operation->quantity = $_POST["initialInventory"];
    $operation->expiration_date = $_POST["expirationDate"];
    $operation->lot = $_POST["lot"];
    $add = $operation->addInput();  

  }
  print "<script>window.location='index.php?view=supplies/index';</script>";

}
?>
