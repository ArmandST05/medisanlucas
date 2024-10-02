<?php
$hoy=date('Y-m-d');
?>
<script src="assets/jquery.btechco.excelexport.js"></script>
<script src="assets/jquery.base64.js"></script>
<script type="text/javascript">
  $(document).ready(function () {

      $("#btnExport").click(function (e) {

          $("#datosexcel").btechco_excelexport({
                  containerid: "datosexcel"
                 , datatype: $datatype.Table
                 , filename: 'Reporte de utilidad'
          });

      });

  });
</script>
<section class="content">
<div class="row">
	<div class="col-md-12">
	<h1>Reporte de utilidad</h1>


						<form>
						<input type="hidden" name="view" value="utilidad">
<div class="row">

<div class="col-md-3">
<input type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }?>" class="form-control">
</div>
<div class="col-md-3">
<input type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }?>" class="form-control">
</div>

<div class="col-md-3">
<input type="submit" class="btn btn-success btn-block" value="Procesar">
</div>
<div class="col-md-3">
 <input type="submit" class="btn btn-primary btn-block" value="Exportar" id="btnExport">

</div>
</div>

</form>

	</div>
	</div>
<br><!--- -->
<div class="row">
	
	<div class="col-md-12">

		<?php

         $f1=isset($_GET["sd"])  ? $_GET['sd'] : null ;
         $f2=isset($_GET["ed"])  ? $_GET['ed'] : null ;

		 $ConIng=OperationData::getAllSellDateUt($f1,$f2);
		 $ConEgre=OperationData::getAllExpensesByDateUt($f1,$f2);
		 
	?>
	
<div class="clearfix"></div>
<h2 id="uti" name="uti"> </h2>
<h3>Ingresos</h3><br>
<table class="table table-bordered table-hover" style="width:750px;" id='datosexcel' border='1'>
	<thead >
		    <th>Conceptos</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Resumen</th>
			</thead>

	<?php
   $tot=0;
	 foreach($ConIng as $con){
	 	      
				$cat = OperationData::getCatePro($con->product_id);
                $res = OperationData::getAllMedicSalesByProductDateUt($con->product_id,$f1,$f2);
                $pro = ProductData::getById($con->product_id);
                //$res = OperationData::getAllExpensesByDateR($con->id,$fecha);
                 if($cat->idCat<>8){
                 	$tot +=$con->price;
				echo "
				<tr class='success'>
				<td>$pro->name</td>
                <td>$con->q</td>
                <td>".number_format($con->price,2)."</td>
                <td>
				";
				                				
               foreach ($res as $key1) {
					
					$med = OperationData::getMedic($key1->idMedic);
					echo "$med->name <label>Can: </label> $key1->q <label>Total: </label> ".number_format($key1->price,2)."<br>";
					
				}

				echo "</td></tr>";
			     }
               }

               /****Nuevo*************/
             
               
               $Cat = OperationData::getNameCat(8);
               $cM=0; $tM=0;
	       foreach($Cat as $c){
	       		 
	       		 $det = OperationData::getdetMed($f1,$f2);
	       	     foreach ($det as $k) {
				 $cM +=$k->q;  $tM +=$k->total;
				 }
                echo "
				<tr class='success'>
				<td>".$c->name."</td>
				";
  

			
               
              	echo "
				<td>".$cM."</td>
				<td>".number_format($tM,2)."</td>
                <td>
                ";
				 foreach ($det as $k) {
				 	$pro = ProductData::getById($k->product_id);
				 echo "$pro->name <label>Can: </label> $k->q <label>Total: </label> ".number_format($k->price,2)."<br>";
					
				 }
				

				echo "</td></tr>";
			
               }
            
            echo "<tr><td><label>Total:</label></td><td></td><td class='success'><label>".number_format($tot+$tM,2)."</label></td></tr>";
		  	 ?>
		    
		    <input type="hidden"  id="ingre" name="ingre" value="<?php echo $tot+$tM ?>">
			</table>

<h3>Entradas</h3><br>
<table class="table table-bordered"  style="width:350px;" id='datosexcel'>
	<thead >
		    <th>Forma de pago</th>
            <th>Total</th>
            <th>Facturado</th>
            <th>Banco Santander</th>
            <th>Banco Banorte</th>
           	</thead>

	<?php

	
	$typ = PaymentTypeData::getAll();
	$totGen=0; $totFac=0; $totSan=0; $totBan=0;

	 foreach($typ as $t){

      $ing = OperationData::getIngresosUt($t->id,$f1,$f2);
        
	 	
	 	   echo "
				<tr class='success'>
				<td>$t->name</td>";
				$toti=0; $totf=0;  $tots=0;  $totb=0;
				foreach ($ing as $i) {
					 $totGen+= $i->total;
					 $toti += $i->total;
					 /****** FACTURADO *******/
					 if($i->fac==1){
                     $totf += $i->total;
                     $totFac += $i->total;
					 }
					 /****** BANCO *******/
					 if($i->banco==1){
                     $totb += $i->total;
                     $totBan += $i->total;
					 }else{
					 $tots += $i->total;
					 $totSan += $i->total;
					 }
				}
				echo "<td>".number_format($toti,2)."</td>
				      <td>".number_format($totf,2)."</td>
				      <td>".number_format($tots,2)."</td>
				      <td>".number_format($totb,2)."</td>";

                 echo "</tr>";   
           
          }
            
            echo "<tr><td><label>Total:</label></td><td class='success'><label>".number_format($totGen,2)."</label></td><td class='success'><label>".number_format($totFac,2)."</label></td><td class='success'><label>".number_format($totSan,2)."</label></td><td class='success'><label>".number_format($totBan,2)."</label></td></tr>";
		  	 ?>
		    
		
			</table>


			<h3>Gastos</h3><br>
<table class="table table-bordered table-hover"  style="width:750px;" id='datosexcel' border='1'>
	<thead>
		    <th>Conceptos</th>
            <th>Cantidad</th>
            <th>Total</th>
        
			</thead>

	<?php
   $totE=0;
	 foreach($ConEgre as $conE){
	 	      $totE +=$conE->price * $conE->q;
				$pro = ProductData::getById($conE->product_id);
                //$res = OperationData::getAllExpensesByDateR($con->id,$fecha);
				echo "
				<tr class='danger'>
				<td>$pro->name</td>
                <td>$conE->q</td>
                <td>".number_format($conE->price * $conE->q,2)."</td>
				";
                
				
				echo "</tr>";
              }
            
            echo "<tr ><td><label>Total:</label></td><td></td><td class='danger'><label>".number_format($totE,2)."</label></td></tr>";
		  	 ?>
		    
		
			</table>


<h3>Salidas</h3><br>
<table class="table table-bordered"  style="width:350px;" id='datosexcel' border='1'>
	<thead >
		    <th>Forma de pago</th>
            <th>Total</th>
            <th>Facturado</th>
            <th>Banco Santander</th>
            <th>Banco Banorte</th>
           	</thead>

	<?php

	
	$typE = OperationData::getTypepaymentE();
	$totGenE=0; $totFacE=0; $totSanE=0; $totBanE=0;

	 foreach($typE as $t){

      $eg = OperationData::getEgresosEUt($t->id,$f1,$f2);
        
	 	
	 	   echo "
				<tr class='success'>
				<td>$t->name</td>";
				$totiE=0; $totfE=0;  $totsE=0;  $totbE=0;
				foreach ($eg as $e) {
					 $totGenE+= $e->total;
					 $totiE += $e->total;
					 /****** FACTURADO *******/
					 if($e->fac==1){
                     $totfE += $e->total;
                     $totFacE += $e->total;
					 }
					 /****** BANCO *******/
					 if($e->banco==1){
                     $totbE += $e->total;
                     $totBanE += $e->total;
					 }else{
					 $totsE += $e->total;
					 $totSanE += $e->total;
					 }
				}
				echo "<td>".number_format($totiE,2)."</td>
				      <td>".number_format($totfE,2)."</td>
				      <td>".number_format($totsE,2)."</td>
				      <td>".number_format($totbE,2)."</td>";

                 echo "</tr>";   
           
          }
            
            echo "<tr><td><label>Total:</label></td><td class='success'><label>".number_format($totGenE,2)."</label></td><td class='success'><label>".number_format($totFacE,2)."</label></td><td class='success'><label>".number_format($totSanE,2)."</label></td><td class='success'><label>".number_format($totBanE,2)."</label></td></tr>";
		  	 ?>
		    
		 <input type="hidden"  id="egre" name="egre" value="<?php echo $totGenE ?>">
			</table>
	
<br><br><br><br><br><br><br><br><br><br>
	</div>
</div>
<script type="text/javascript">
	
$(document).ready(function(){

	
	
var tot=(parseFloat($('#ingre').val()) - parseFloat($('#egre').val()));
var tot2=tot.toFixed(2);
//alert(tot);

  $('#uti').html("Utilidad: "+tot2);
});

</script>