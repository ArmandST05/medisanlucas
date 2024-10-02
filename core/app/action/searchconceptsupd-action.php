	<?php

$concepts = ExpenseCategoryData::getCatExpense($_GET["concept"]);

if(count($concepts)>0){
	?>
<h3>Resultados de la Busqueda</h3>
<table class="table table-bordered table-hover">
	<thead>
		<th>Id</th>
		<th>Nombre concepto</th>
		<th>Categoría<th>
		<th></th>
	</thead>
	<?php


     foreach($concepts as $con){
     
	echo '
	    <tr>
		<td style="width:80px;">'.$con->id.'</td>
		<td>'.$con->name.'</td>
		<td>'.$con->nameCat.'</td>
		<td>
		<form method="post" action="index.php?view=addbuyupd" autocomplete="off">
		<input type="hidden" name="idCon" value="'.$con->id.'">
       	<input type="hidden" name="idBuy" value="'.$_GET["idBuy"].'">
      <span class="input-group">
       <input type="number" class="form-control" autofocus required name="cost"  placeholder="Costo ...">
      <span class="input-group-btn">
		<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign"></i> Agregar</button>
      </span></td>
      </div>
  		</form>
	</tr>';

	}
?>
	<?php

}else{
	echo "<br><p class='alert alert-danger'>No se encontro el producto</p>";

}
?>
<hr><br>
