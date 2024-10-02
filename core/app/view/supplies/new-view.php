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
              alert("Error al obtener los datos.");
            },
            success: function(data) {
              var r = data;
              $("#result").html(data);
              if (r == "") {
                $("#inventoryDetails").show();
              } else {
                $("#inventoryDetails").hide();
              }

            }
          });

        });

      });

    });
  </script>

  <div class="row">
    <div class="col-md-12">
      <h1>Nuevo insumo</h1>
      <div id="result"></div>
      <br>
      <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addsupplies" action="index.php?action=supplies/add" role="form" autocomplete="off">
        <div class="form-group">
          <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
          <div class="col-md-6">
            <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre insumo" autofocus>
          </div>
        </div>
        <div style="display:none;" id="inventoryDetails" name="inventoryDetails">
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Mínimo en inventario:</label>
            <div class="col-md-6">
              <input type="number" min="0" step=".01" name="minimumInventory" required class="form-control" id="inputEmail1" placeholder="Mínimo en inventario">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Inventario inicial:</label>
            <div class="col-md-6">
              <input type="number" min="0" step=".01" name="initialInventory" class="form-control" required id="inputEmail1" placeholder="Inventario inicial">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Fecha expiración inventario inicial:</label>
            <div class="col-md-6">
              <input type="date" min="<?php echo date("Y-m-d") ?>" name="expirationDate" class="form-control" placeholder="Fecha expiración inventario inicial" required>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Lote inventario inicial:</label>
            <div class="col-md-6">
              <input type="text" name="lot" class="form-control" placeholder="Lote inventario inicial" required>
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <button type="submit" class="btn btn-primary">Agregar insumo</button>
            </div>
          </div>
      </form>

    </div>
  </div>
  </div>
