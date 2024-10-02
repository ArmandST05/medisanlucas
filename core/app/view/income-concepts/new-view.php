<div class="row">
  <div class="col-md-12">
    <h1>Agregar concepto</h1>
    <div id="result"></div>
    <br>
    <form class="form-horizontal" method="post" enctype="multipart/form-data" autocomplete='off' action="index.php?action=income-concepts/add" role="form">

      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" class="form-control" id="name" autofocus placeholder="Nombre">
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Descripción</label>
        <div class="col-md-6">
          <textarea name="description" class="form-control" id="description" autocomplete='Off' rows="5"></textarea>
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Precio de Salida*</label>
        <div class="col-md-6">
          <input type="number" step=".01" min="0" name="priceOut" required class="form-control" id="priceOut" placeholder="Precio de salida">
        </div>
      </div>
      <div style="display:none;" id="divBtnSave" name="divBtnSave">
        <div class="form-group">
          <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" class="btn btn-primary">Agregar concepto</button>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>
</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    var name
    //hacemos focus
    $("#name").focus();
    //comprobamos si se pulsa una tecla
    $("#name").keyup(function(e) {
      //obtenemos el texto introducido en el campo
      name = $("#name").val();
      //hace la búsqueda
      $("#result").delay(1000).queue(function(n) {

        $("#result").html();

        $.ajax({
          type: "POST",
          url: "./?action=products/validate-name",
          data: "name=" + name,
          dataType: "html",
          error: function() {
            alert("Error al validar el nombre del concepto.");
          },
          success: function(data) {
            $("#result").html(data);
            if (data == "") {
              $("#divBtnSave").show();
            } else {
              $("#divBtnSave").hide();
            }

          }
        });

      });

    });

  });
</script>