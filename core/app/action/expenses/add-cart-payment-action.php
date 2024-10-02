<?php
$found = false;
if(!isset($_SESSION["expensePaymentTypes"])){

	$type_pay = array("id"=>$_POST["paymentType"],"total"=>$_POST["total"]);
	$_SESSION["expensePaymentTypes"] = array($type_pay);
	$typePay = $_SESSION["expensePaymentTypes"];
}
else{
	$index=0;
	$typePay = $_SESSION["expensePaymentTypes"];

foreach($typePay as $c){
	if($c["id"]==$_POST["paymentType"]){
		$found=true;
		break;
	}
	$index++;

}

if($found==true){
	
	echo '<script> 
			alert("El tipo de pago ya está en la lista");
		window.location="index.php?view=expenses/new";
		</script>
	';
					
}

if($found==false){
    $nc = count($typePay);
	$type_pay = array("id"=>$_POST["paymentType"],"total"=>$_POST["total"]);
	$typePay[] = $type_pay;
	$_SESSION["expensePaymentTypes"] = $typePay;

}

}
 print "<script>window.location='index.php?view=expenses/new';</script>";

?>