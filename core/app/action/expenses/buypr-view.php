<?php 
 
$typePay = PaymentTypeData::getAll();
$total = 0;
$totalPay=0;
 ?>
	<div class="row">
	<div class="col-md-12">
	<h1>Gastos</h1>
	<p><b>Buscar por Concepto/Medicamentos/Categoría:</b></p>
		<form>
		<div class="row">
			<div class="col-md-6">
				<input type="hidden" name="view" value="buy">
				<input type="text" name="concept" class="form-control">
			</div>
			<div class="col-md-3">
			<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Buscar</button>
			</div>
		</div>
		</form>
	</div>

	<?php
if(isset($_GET["concept"])):?>
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
     
	echo '<form method="post" action="index.php?view=addbuy" autocomplete="off">
		
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
		 </td>
      	
	</tr></form>
	';

	}
?>
	<?php

}else{
	echo "<br><p class='alert alert-danger'>No se encontro el conceptos/medicamentos</p>";

}
?>
<hr><br>
<?php endif; ?>

<?php if(isset($_SESSION["expense"])):

?>
<h2>Lista</h2>
<table class="table table-bordered table-hover">
<thead>
	<th style="width:30px;">ID</th>
	<th style="width:250px;">Concepto</th>
	<th style="width:250px;">Categoría</th>
	<th style="width:30px;">Cantidad</th>
	<th style="width:30px;">Costo</th>
	<th ></th>
</thead>
<?php foreach($_SESSION["expense"] as $c):
$concept = ExpenseCategoryData::getByExpenseCategoryId($c["idCon"]);
?>
<tr >
	<td><?php echo $c["idCon"]; ?></td>
	<td><?php echo $concept->name; ?></td>
	<td><?php echo $concept->nameCat; ?></td>
	<td><?php echo $c["q"]; ?></td>
	<td><b>$ <?php echo number_format($c["cost"]); ?></b></td>
	 <?php  $pt = $c["cost"]*$c["q"]; $total +=$pt;?>
	<td style="width:30px;"><a href="index.php?view=clearbuy&idCon=<?php echo $concept->id; ?>" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Cancelar</a></td>
</tr>

<?php endforeach; ?>
</table>

<h2>Resumen</h2>

  <form method="post" action="index.php?view=addpaytobuy" autocomplete="off">

 <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Forma de pago</label>
    <div class="col-lg-4">
     <select name="idTypePay" class="form-control" required>
    <option value="">-- SELECCIONE --</option>      
    <?php foreach($typePay as $type):?>
    <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>      
    <?php endforeach;?>
    </select>
    </div>
     <div class="col-lg-3">
      <input type="number" name="money" required class="form-control" id="money" placeholder="Total">
    </div>
    <div class="col-md-2">
	 <button type="submit" class="btn btn-primary"><i class=""></i> Agregar</button>
	</div>
  </div>
 </form>
  <div class="row">
<div class="col-md-6">
<table class="table table-bordered">


<?php if(isset($_SESSION["expensePaymentTypes"])):

?>

<table class="table table-bordered table-hover">
<thead>
	<th style="">ID</th>
	<th style="">Forma de pago</th>
	<th style="">Total</th>
	<th ></th>
</thead>

<?php foreach($_SESSION["expensePaymentTypes"] as $t):
$tPay = PaymentTypeData::getById($t["idType"]);
?>
<tr >
	<td st><?php echo $t["idType"]; ?></td>
	<td><?php echo  $tPay->name;?></td>
	<td><b>$ <?php  $tp = $t["money"]; $totalPay +=$tp; echo number_format($tp); ?></b></td>
	<td style="width:25px;"><a href="index.php?view=clearpayBu&idTypePay=<?php echo $tPay->id; ?>" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</table>
<?php if($totalPay>0):

?>
<div class="col-lg-4 col-md-offset-6" >
<input  style="text-align:right;" type="text" id="totalGen1" name="totalGen1" value="<?php echo number_format($totalPay,2) ?>"class="form-control">
<?php endif; ?>
</div>
</div>


<div class="col-md-6">
<table class="table table-bordered">
<tr>
	<td><p>Subtotal</p></td>
	<td><p><b>$ <?php echo number_format($total*.84); ?></b></p></td>
</tr>
<tr>
	<td><p>IVA</p></td>
	<td><p><b>$ <?php echo number_format($total*.16); ?></b></p></td>
</tr>
<tr>
	<td><p>Total</p></td>
	<td><p><b>$ <?php echo number_format($total); ?></b></p></td>
</tr>

</table>
</div>
</div>

<form method="post" class="form-horizontal" id="processbuy" action="index.php?view=processbuy">

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <div class="checkbox">
        <label>
          <input name="is_oficial" type="hidden" value="1">
        </label>
      </div>
    </div>
  </div>
<div class="form-group">
    <div class="col-lg-offset-6 col-lg-10">
      <div class="checkbox">
        <label>
		<a href="index.php?view=clearbuyt" class="btn btn-lg btn-danger"><i class="glyphicon glyphicon-remove"></i> Cancelar</a>
        <button class="btn btn-lg btn-primary"><i class="glyphicon glyphicon-usd"></i><i class="glyphicon glyphicon-usd"></i> Finalizar</button>
        </label>

    <input type="hidden" name="total" value="<?php echo $total; ?>" class="form-control" placeholder="Total">
	<input type="hidden" id="discount" name="discount" value="0"class="form-control">
    <input type="hidden" id="totalGen" name="totalGen" value="<?php echo $totalPay ?>"class="form-control">


      </div>
    </div>
  </div>
</form>
<script>
	$("#processbuy").submit(function(e){
		discount = $("#discount").val();
		money = $("#totalGen").val();
		if(money>(<?php echo $total;?>-discount)){
			alert("No se puede efectuar la operacion verifica tus cantidades");
			e.preventDefault();
		}else{
			if(discount==""){ discount=0;}
			//go = confitotalpagos = $("#totalGen").val();rm("Cambio: $"+(money-(<?php echo $total;?> ) ) );
			if(go){}
				else{e.preventDefault();}
		}
	});
</script>
</div></div>

<?php endif; ?>
<br><br><br><br><br>



</div>

