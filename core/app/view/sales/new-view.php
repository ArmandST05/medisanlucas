<?php
$patients = PatientData::getAll();
?>
<div class="row">
  <div class="col-md-12">
    <h1>Nueva venta</h1>
    <form class="form-horizontal" role="form" method="GET">
      <input type="hidden" name="view" value="sales/new-details">
      <div class="form-group">
        <div class="col-lg-3">
          <label for="inputEmail1" class="col-lg-3 control-label">Paciente</label>
          <select name="patientId" id="patientId" class="form-control" id="combobox" autofocus required>
            <option value="">-- SELECCIONE --</option>
            <?php foreach ($patients as $patient) : ?>
              <option value="<?php echo $patient->id; ?>"><?php echo $patient->id . " - " . $patient->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-3">
          <label for="inputEmail1" class="col-lg-3 control-label">Fecha</label>
          <input id="date" type="date" class="form-control" value="<?php echo date('Y-m-d');?>" name="date" required>

        </div>
        <div class="col-lg-3">
          <br>
          <button type="submit" class="btn btn-primary">Generar venta</button>
        </div>
      </div>
  </div>

  </form>
</div>
</div>

</div>
<!--/.content-->
</div>
<!--/.span9-->
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#patientId").select2({});
  });
</script>