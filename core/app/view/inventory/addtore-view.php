<?php

if(!isset($_SESSION["reabastecer"])){


	$product = array("product_id"=>$_POST["product_id"],"q"=>$_POST["q"],"precio"=>$_POST["price"]);
	$_SESSION["reabastecer"] = array($product);


	$cart = $_SESSION["reabastecer"];

///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////

$process=true;


}else {

$found = false;
$cart = $_SESSION["reabastecer"];
$index=0;

$q = OperationDetailData::getStockByProduct($_POST["product_id"]);





$can = true;

?>

<?php
if($can==true){
foreach($cart as $c){
	if($c["product_id"]==$_POST["product_id"]){
		echo "found";
		$found=true;
		break;
	}
	$index++;
//	print_r($c);
//	print "<br>";
}

if($found==true){
	echo '<script> 
			alert("El producto ya esta en la lista");
			window.location="index.php?view=re";
		</script>
	';
}

if($found==false){
    $nc = count($cart);
	$product = array("product_id"=>$_POST["product_id"],"q"=>$_POST["q"],"precio"=>$_POST["price"]);
	$cart[$nc] = $product;
//	print_r($cart);
	$_SESSION["reabastecer"] = $cart;
}

}
}
print "<script>window.location='index.php?view=re';</script>";
// unset($_SESSION["reabastecer"]);

?>