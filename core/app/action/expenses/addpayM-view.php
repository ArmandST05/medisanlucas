<?php

if(!isset($_SESSION["typePM"])){

	$type_pay = array("idType"=>$_POST["idTypePay"],"money"=>$_POST["money"]);
	$_SESSION["typePM"] = array($type_pay);
	$typePay = $_SESSION["typePM"];

	
}

else{
	$index=0;
	$typePay = $_SESSION["typePM"];

foreach($typePay as $c){
	if($c["idType"]==$_POST["idTypePay"]){
		//echo "found";
		$found=true;
		break;
	}
	$index++;

}

if($found==true){
	
	echo '<script> 
			alert("El tipo de pago ya esta en la lista");
		window.location="index.php?view=re";
		</script>
	';
					
}

if($found==false){
    $nc = count($typePay);
	$type_pay = array("idType"=>$_POST["idTypePay"],"money"=>$_POST["money"]);
	$typePay[$nc] = $type_pay;
    //print_r($cart);
	$_SESSION["typePM"] = $typePay;

    //echo "entre2";
	 

}


}
 print "<script>window.location='index.php?view=re';</script>";

?>