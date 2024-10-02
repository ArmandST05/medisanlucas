<div class="row">
  <div class="col-md-12">
    <h1>Agregar categoría</h1>
    <div id="result"></div>
    <br>
    <form class="form-horizontal" method="post" enctype="multipart/form-data" autocomplete='off' action="index.php?action=expense-categories/add" role="form">
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre" autofocus>
        </div>
      </div>
      <div style="display:none;" id="divBtnAdd">
        <div class="form-group">
          <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" class="btn btn-primary">Agregar</button>
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
    //hacemos focus
    $("#name").focus();
    //comprobamos si se pulsa una tecla
    $("#name").keyup(function(e) {
      //Obtenemos el texto introducido en el campo
      let name = $("#name").val();
      //Realizar la búsqueda
      $("#result").delay(1000).queue(function(n) {

        $("#result").html();
        $.ajax({
          type: "POST",
          url: "./?action=expense-categories/validate-name",
          data: "name=" + name,
          dataType: "html",
          error: function() {
            alert("Error al consultar los datos.");
          },
          success: function(data) {
            $("#result").html(data);
            if (data == "") {
              $("#divBtnAdd").show();
            } else {
              $("#divBtnAdd").hide();
            }
          }
        });
      });
    });

  });
</script>