<?php
    $products = ProductData::getById($_POST["valor"]);  
    echo $products->price_out;
?>