
<?php if(isset($_GET["product"]) && $_GET["product"]!=""):?>
	<?php

$products = ProductData::getLikeSal($_GET["product"]);

if(count($products)>0){
	?>
<h3>Resultados de la Busqueda</h3>
<table class="table table-bordered table-hover">
	<thead>
		<th>Codigo</th>
		<th>Nombre</th>
		<th>En inventario</th>
		<th></th>
		
		<th></th>
	</thead>
	<?php
$products_in_cero=0;

     foreach($products as $product):
     	
        $q= OperationDetailData::getStockByProduct($product->id);
       
	?>
	<?php 
	if($q>0):?>
		
	<tr class="<?php if($q<=$product->minimum_inventory){ echo "danger"; }?>">
		<td style="width:80px;"><?php echo $product->id; ?></td>
		<td><?php echo $product->name; ?></td>
		<!--td><b>$<?php echo $product->price_out; ?></b></td-->
		<?php
      	echo "<td>
			 $q
		</td>";
	
		 	?>

		 <td>
		<form method="post" action="index.php?view=addtocartSal" autocomplete="off">
		<input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
        <input type="hidden" name="type" value="<?php echo $product->type; ?>">
      
  
      <span class="input-group">
       <input type="number" class="form-control" value="1" autofocus required name="q" placeholder="Cantidad ...">
      <span class="input-group-btn">
		<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign"></i> Agregar</button>
      </span></td>
      </div>
  		</form>
	</tr>
	
<?php else:$products_in_cero++;
?>
<?php  endif; ?>
	<?php endforeach;?>
</table>
<?php if($products_in_cero>0){ echo "<p class='alert alert-warning'>Se omitieron <b>$products_in_cero productos</b> que no tienen existencias en el inventario. <a href='index.php?module=inventary'>Ir al Inventario</a></p>"; }?>

	<?php
}else{
	echo "<br><p class='alert alert-danger'>No se encontraron resultados</p>";
	
}
?>
<hr><br>
<?php else:
?>
<?php endif; ?>