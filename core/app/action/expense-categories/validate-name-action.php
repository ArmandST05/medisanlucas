<?php
$name = $_POST['name'];
$category = ExpenseCategoryData::getByName($name);

if(!empty($category)){
    echo "<span style='font-weight:bold;color:red;
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;'>El nombre ".$name." ya existe </span>";
  }else{
    echo "";
  }
?>