<?php
$found = false;
if(!isset($_SESSION["expense"])){
	$type_pay = array("id"=>$_POST["conceptId"],"cost"=>$_POST["cost"],"quantity"=>$_POST["quantity"],"expirationDate"=>$_POST["expirationDate"]);
	$_SESSION["expense"] = array($type_pay);
	$typeBuy = $_SESSION["expense"];	
}

else{
	$index=0;
	$typeBuy = $_SESSION["expense"];

foreach($typeBuy as $c){
	if($c["id"]==$_POST["conceptId"]){
		$found=true;
		break;
	}
	$index++;

}

if($found==true){
	echo '<script> 
	    alert("El producto ya está en la lista");
		window.location="index.php?view=expenses/new";
		</script>
	';				
}

if($found==false){
	$type_buy = array("id"=>$_POST["conceptId"],"cost"=>$_POST["cost"],"quantity"=>$_POST["quantity"],"expirationDate"=>$_POST["expirationDate"]);
	$typeBuy[] = $type_buy;
	$_SESSION["expense"] = $typeBuy;
}

}
 print "<script>window.location='index.php?view=expenses/new';</script>";
