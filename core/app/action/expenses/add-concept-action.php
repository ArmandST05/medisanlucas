<?php

if(count($_POST)>0){
 $op = new OperationDetailData();
 $op->product_id = $_POST["conceptId"];
 $op->operation_type_id=1; // 1 - entrada
 $op->operation_id= $_POST["expenseId"];
 $op->quantity= $_POST["quantity"];
 $op->price= $_POST["cost"];
 $op->date = date("Y-m-d H:i:s"); 
 $op->expiration_date= $_POST["expirationDate"];
 $add = $op->addExpense();		

print "<script>window.location='index.php?view=expenses/edit&id=".$_POST["expenseId"]."';</script>";
}


?>