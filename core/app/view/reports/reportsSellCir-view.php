<script src="assets/jquery.btechco.excelexport.js"></script>
<script src="assets/jquery.base64.js"></script>
<script type="text/javascript">
  $(document).ready(function () {

      $("#btnExport").click(function (e) {

          $("#datosexcel").btechco_excelexport({
                  containerid: "datosexcel"
                 , datatype: $datatype.Table
                 , filename: 'Reporte Cirugías por cobrar y cobradas'
          });

      });

  });
</script>
<section class="content">
<div class="row">
	<div class="col-md-12">
	<h1>Reporte Cirugías por cobrar y cobradas </h1>

						<form>
						<input type="hidden" name="view" value="reportsSellCir">
<div class="row">

<div class="col-md-2">
<input type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }?>" class="form-control">
</div>
<div class="col-md-2">
<input type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }?>" class="form-control">
</div>
<div class="col-md-3">
<input type="text" name="name" value="" class="form-control" autocomplete="off">
</div>

<div class="col-md-2">
<input type="submit" class="btn btn-success btn-block" value="Procesar">
</div>
<div class="col-md-2">
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
    $name=isset($_GET["name"])  ? $_GET['name'] : null ;
 
		 $curr_sell = OperationData::getAllSellFechasCir($f1,$f2,$name);
		 
		
         

if(count($curr_sell)>0){


	?>
	
<div class="clearfix"></div>
<h3>Cobradas</h3>

<table class="table table-bordered table-hover" id='datosexcel' border='1'>
	<thead>
           
		    <th>Folio</th>
		    <th>Fecha</th>
			<th>Nombre del paciente</th>
            <th>Comentarios</th>
            <th>Hospi-Cir</th>  
			<th>Total</th>
			<th>Forma de pago</th>
			<th>Facturado</th>
			<th>No de Factura</th>
			<th>Banco</th>
	     	
			</thead>

	<?php
     $tot=0;
	 foreach($curr_sell as $sell){
            $tot +=$sell->total;

                 $sta=$sell->status;
                 if($sta==1){
                     $t="success";
                 }else{
                     $t="danger";
                 }
	 	        $tPa="";$tBa="";

				$typeP = OperationPaymentData::getAllByOperationId($sell->id);
				echo "<tr class='$t'>
               
				<td style=''>$sell->id</td>
				<td style=''>".$sell->nombre_dia.", ".$sell->fecha."</td>
				<td style=''>".$sell->name."</td>
                <td style=''>$sell->comentarios</td>
                <td style=''>$sell->note</td>
				<td style=''>".number_format($sell->total,2)."</td>
				<td>";
                
                foreach ($typeP as $key) {
                	   
                  echo "$key->tname: ".number_format($key->total,2)."<br>";

                	if($key->tname=="T. DEBITO" || $key->tname=="T. CREDITO")
                	{

                    $tPa="Santander";
                    
                	}else if ($key->tname=="TRANSFERENCIA" || $key->tname=="CHEQUES" || $key->tname=="INST. VIDA" || $key->tname=="SEGUROS" || $key->tname=="STAR MED" || $key->tname=="OTROS"){
                     $tBa="Banco";
                	}else{
                      $tPa="NA";
                	}
                }
				 
	              if($sell->fac==1){
				 echo '
				 <td><label style="color:#2A8AC4">Facturar</label></td>
				 ';
                 }else{
                 echo '
                  <td><label style="color:#FF5F5F">No Facturar</label></td>';
                }
                echo '<td>'.$sell->noFac.'</td>';
                if ($tBa=="Banco"){
                
                 if($sell->banco ==0){
                 	echo ' <td><label>Santander</label></td>';
                 }
                 else if($sell->banco ==1){
                 	echo '<td><label>Banorte</label></td>';
                 }
                 
                }
                else if ($tPa=="Santander"){
                echo "<td><label>Santander</label></td>";
                }
                else
                echo "<td><label>No aplica</label></td>";
                }
              

              
            
		  	 ?>
		   
		      
			</table>
            <h3 style="color:#2A8AC4">Cobradas: <?php echo number_format($tot,2) ?></h3>
 
 <?php



    }else{
      echo "<p class='alert alert-danger'>No se encontraron resultados</p>";
    }


    ?>

    <?php


 $f1=isset($_GET["sd"])  ? $_GET['sd'] : null ;
  $f2=isset($_GET["ed"])  ? $_GET['ed'] : null ;
 
		 $curr_sellP = OperationData::getAllSellFechasPCir($f1,$f2,$name);
		 
		
         

if(count($curr_sellP)>0){


	?>
	
<div class="clearfix"></div>
<h3>Pendientes por cobrar</h3>

<table class="table table-bordered table-hover" id='datosexcel' border='1'>
	<thead>
           
		    <th>Folio</th>
		    <th>Fecha</th>
			<th>Nombre del paciente</th>
            <th>Comentarios</th>
            <th>Hospi-Cir</th> 
			<th>Total</th>
			<th>Forma de pago</th>
			<th>Facturado</th>
			<th>No de Factura</th>
			<th>Banco</th>
	     	
			</thead>

	<?php
     $totp=0; $totpa=0;
	 foreach($curr_sellP as $sell){
                $totp +=$sell->total;
                 $sta=$sell->status;
                 if($sta==1){
                     $t="success";
                 }else{
                     $t="danger";
                 }
	 	        $tPa="";$tBa="";

				$typeP = OperationPaymentData::getAllByOperationId($sell->id);
				echo "<tr class='$t'>
               	<td style=''>$sell->id</td>
				<td style=''>".$sell->nombre_dia.", ".$sell->fecha."</td>
				<td style=''>".$sell->name."</td>
                <td style=''>$sell->comentarios</td>
                <td style=''>$sell->note</td>
				<td style=''>".number_format($sell->total,2)."</td>
				<td>";
                
                foreach ($typeP as $key) {
                	 $totpa +=$key->total;
                	   
                  echo "$key->tname: ".number_format($key->total,2)."<br>";

                	if($key->tname=="T. DEBITO" || $key->tname=="T. CREDITO")
                	{

                    $tPa="Santander";
                    
                	}else if ($key->tname=="TRANSFERENCIA" || $key->tname=="CHEQUES" || $key->tname=="INST. VIDA" || $key->tname=="SEGUROS" || $key->tname=="STAR MED" || $key->tname=="OTROS"){
                     $tBa="Banco";
                	}else{
                      $tPa="NA";
                	}
                }
				 
	              if($sell->fac==1){
				 echo '
				 <td><label style="color:#2A8AC4">Facturar</label></td>
				 ';
                 }else{
                 echo '
                  <td><label style="color:#FF5F5F">No Facturar</label></td>';
                }
                echo '<td>'.$sell->noFac.'</td>';
                if ($tBa=="Banco"){
                
                 if($sell->banco ==0){
                 	echo ' <td><label>Santander</label></td>';
                 }
                 else if($sell->banco ==1){
                 	echo '<td><label>Banorte</label></td>';
                 }
                 
                }
                else if ($tPa=="Santander"){
                echo "<td><label>Santander</label></td>";
                }
                else
                echo "<td><label>No aplica</label></td>";
                }
              

              
            
		  	 ?>
		   
		      
			</table>
  <h3 style="color:#FF5F5F">Pendiente por cobrar: <?php echo number_format($totp - $totpa,2) ?></h3>
 <?php



    }else{
      echo "<p class='alert alert-danger'>No se encontraron resultados</p>";
    }


    ?>
	</div>
</div>

<br><br><br><br>
</section>