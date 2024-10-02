<?php 
$incomeConcept = ProductData::getById($_GET["id"]);
?>
<div class="row">
  <div class="col-md-12">
    <h1>Editar Concepto</h1>
    <br>
    <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=income-concepts/update" role="form">

      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" class="form-control" id="name" value='<?php echo $incomeConcept->name; ?>' autofocus placeholder="Nombre" autocomplete='Off'>
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Descripción</label>
        <div class="col-md-6">
          <textarea name="description" class="form-control" id="description" rows="5"><?php echo $incomeConcept->description; ?></textarea>
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Precio de Salida*</label>
        <div class="col-md-6">
          <input type="number" step=".01" min="0" name="priceOut" required class="form-control" id="priceOut" value='<?php echo $incomeConcept->price_out; ?>' placeholder="Precio de salida">
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <input type="hidden" name="id" value="<?php echo $incomeConcept->id ?>">
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </div>
    </form>
  </div>
</div>
</div>
</div>