<?php
$product = ProductData::getById($_POST["id"]);  
echo $product->price_in;
?>