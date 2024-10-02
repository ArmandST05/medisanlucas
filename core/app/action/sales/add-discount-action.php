<?php
if (count($_POST) > 0) {
    $discountPercentage = $_POST["discountSale"];
    $operation = OperationData::getById($_POST["saleId"]);
    $operation->discount = (($operation->total*$discountPercentage)/100);
    $operation->discount_percentage = $discountPercentage;
    $operation->updateDiscount();
}
print "<script>window.location='index.php?view=sales/edit&id=" . $_POST["saleId"] . "';</script>";