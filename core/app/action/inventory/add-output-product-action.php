<?php
 $product = ProductData::getById($_POST["id"]);
 $type = $product->getType()->name;

if(!isset($_SESSION["cartOutputs"])){

	$product = array("id"=>$_POST["id"],"quantity"=>$_POST["quantity"],"type"=>$type);
	$_SESSION["cartOutputs"] = array($product);
     
	$cart = $_SESSION["cartOutputs"];
	
		$num_succ = 0;
		$process=false;
		$errors = array();
		foreach($cart as $c){

			$quantity = OperationDetailData::getStockByProduct($c["id"]);
			if($c["quantity"]<=$quantity){
				$num_succ++;
			}else{
				$error = array("id"=>$c["id"],"message"=>"No hay suficiente cantidad de producto en inventario.");
				$errors[count($errors)] = $error;
			}

		}
if($num_succ==count($cart)){
	$process = true;
}
if($process==false){
	unset($_SESSION["cartOutputs"]);
$_SESSION["errorsSal"] = $errors;
	
echo '<script>
	window.location="index.php?view=inventory/new-output";
</script>';

}
}else {

$found = false;
$cart = $_SESSION["cartOutputs"];
$index=0;

$quantity = OperationDetailData::getStockByProduct($_POST["id"]);

$can = true;
if($_POST["quantity"]<=$quantity){
}else{
	$error = array("id"=>$_POST["id"],"message"=>"No hay suficiente cantidad de producto en inventario.");
	$errors[count($errors)] = $error;
	$can=false;
}

if($can==false){
$_SESSION["errorsSal"] = $errors;
echo '<script>
	window.location="index.php?view=inventory/new-output";
</script>';
}
?>

<?php
if($can==true){
foreach($cart as $c){
	if($c["id"]==$_POST["id"]){
		$found=true;
		break;
	}
	$index++;

}

if($found==true){
	echo '<script> 
			alert("El producto ya está en la lista");
			window.location="index.php?view=inventory/new-output";
		</script>
	';			
}

if($found==false){
	$product = array("id"=>$_POST["id"],"quantity"=>$_POST["quantity"],"type"=>$type);
	$cart[] = $product;
	$_SESSION["cartOutputs"] = $cart;	 
}
}
}
 print "<script>window.location='index.php?view=inventory/new-output';</script>";
?>