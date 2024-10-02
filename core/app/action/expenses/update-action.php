<?php
    $operation = OperationData::getById($_POST["expenseId"]);
    
    $tot = $_POST["total"];
    $totalGen=$_POST["totalGen"];
    $liq = $tot - $totalGen;

    if($liq == 0){
        $operation->status_id = 1;
    }else{
        $operation->status_id = 0;
    }
    $operation->total = $_POST["total"];
    $operation->description = $_POST["description"];
    $updated = $operation->updateExpense();

print "<script>window.location='index.php?view=expenses/index';</script>";
?>
