<?php
if(count($_POST)>0){
    $date = date("d-m-Y (H:i:s)");
    $operation = new OperationDetailData();
    $operation->product_id = $_POST["id"];
    $operation->operation_type_id = 1;
    $operation->operation_id = 0;
    $operation->quantity = $_POST["quantity"];
    $operation->price = 0;
    $operation->lot = $_POST["lot"];
    $operation->expiration_date = $_POST["expirationDate"];
    $operation->date = $date;
    $add = $operation->addInput();  

    $product = ProductData::getById($_POST["id"]);
    if($product->type_id == 3){//Insumos
        print "<script>window.location='index.php?view=inventory/index-supplies';</script>";
    }else{
        print "<script>window.location='index.php?view=inventory/index-products';</script>";
    }
}
?>
