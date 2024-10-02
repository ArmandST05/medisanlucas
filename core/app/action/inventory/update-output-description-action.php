<?php
  $output = OperationData::getById($_POST["operationId"]);
  $output->description = $_POST["description"];
  $update = $output->updateDescription();  

  print "<script>window.location='index.php?view=inventory/edit-output&id=".$_POST["operationId"]."';</script>";
?>
