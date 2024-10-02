<?php

if(!isset($_SESSION["cart"])){


	$product = array("product_id"=>$_POST["product_id"],"price_out"=>$_POST["price_out"],"q"=>$_POST["q"]);
	$_SESSION["cart"] = array($product);

  
  echo "entre";
	$cart = $_SESSION["cart"];
}
///////////////////////////////////////////////////////////////////
		?>

<?php

    $nc = count($cart);
	$product = array("product_id"=>$_POST["product_id"],"q"=>$_POST["q"]);
	$cart[$nc] = $product;
//	print_r($cart);
	$_SESSION["cart"] = $cart;
	 print "<script>window.location='index.php?view=sell';</script>";

// unset($_SESSION["cart"]);

?>