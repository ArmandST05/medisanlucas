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
     
	echo ' <form method="post" action="index.php?view=addbuy" autocomplete="off">
		
	    <tr>
		<td style="width:80px;">'.$con->id.'</td>
		<td>'.$con->name.'</td>
		<td>'.$con->nameCat.'</td>
		<td>
		
		<input type="hidden" name="idCon" value="'.$con->id.'">
    
      <input type="number" value="'.$con->price_in.'" class="form-control" required name="costo" required placeholder="Costo ..."></td>
		</td><td>
      <input type="number" class="form-control" autofocus required name="q" placeholder="Cantidad ...">
      </td><td>
		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-refresh"></i> Agregar</button>
		 </span></td>
      </div>
  		</form>
	</tr>
	';

	}
?>
	<?php

}else{
	echo "<br><p class='alert alert-danger'>No se encontro el conceptos/medicamentos</p>";

}
?>
<hr><br>
