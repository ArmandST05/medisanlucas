<?php
$name = $_POST['name'];
$product = ProductData::getByName($name);

if(!empty($product)){
  echo "<span style='font-weight:bold;color:red;
  color: #fff;
  background-color: #d9534f;
  border-color: #d43f3a;'>El nombre ".$name." ya existe.</span>";
}else{
  echo "";
}
?>