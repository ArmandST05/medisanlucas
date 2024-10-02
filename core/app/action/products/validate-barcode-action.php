<?php
$barcode = $_POST['barcode'];
$product = ProductData::getByBarcode($barcode);

if(!empty($product)){
    echo "<span style='font-weight:bold;color:red;
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;'>El Código de barras ".$barcode." ya existe </span>";
  }else{
        
    echo "";
  }
?>