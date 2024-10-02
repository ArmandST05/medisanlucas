<?php
$categories = ExpenseCategoryData::getAll();
?>
<div class="row">
  <div class="col-md-12">
    <h1>Agregar concepto</h1>
    <div id="result"></div>
    <br>
    <form class="form-horizontal" method="post" enctype="multipart/form-data" autocomplete='off' action="index.php?action=expense-concepts/add" role="form">
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" class="form-control" id="name" autofocus placeholder="Nombre" required>
        </div>
      </div>
      <div style="display:none;" id="divCategory">
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Categoría gastos</label>
          <div class="col-md-6">
            <select name="category" class="form-control" required>
              <option value="">-- SELECCIONE --</option>
              <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" class="btn btn-primary">Agregar concepto</button>
          </div>
        </div>
    </form>
  </div>
</div>
</div>
</div>
</div>

<script type="text/javascript">
  $(document).ready(function() {

    //hacemos focus
    $("#name").focus();
    //comprobamos si se pulsa una tecla
    $("#name").keyup(function(e) {
      //obtenemos el texto introducido en el campo
      let name = $("#name").val();
      //hace la búsqueda
      $("#result").delay(1000).queue(function(n) {

        $("#result").html();
        $.ajax({
          type: "POST",
          url: "./?action=products/validate-name",
          data: "name=" + name,
          dataType: "html",
          error: function() {
            alert("Error en la consulta");
          },
          success: function(data) {
            $("#result").html(data);
            if (data == "") {
              $("#divCategory").show();
            } else {
              $("#divCategory").hide();
            }

          }
        });

      });

    });

  });
</script>