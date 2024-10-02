<?php
$configuration = ConfigurationData::getAll();
?>
<div class="row">
  <div class="col-md-12">
    <h1>Configuración de perfil general</h1>
    <br>
    <div class="box box-primary">
      <div class="box-body">
        <form class="form-horizontal" method="POST" action="./?action=configuration/update-clinic-profile" role="form" enctype="multipart/form-data">
          <?php if ($configuration['logo']->value) : ?>
            <div class="col-md-3">
              <img class="img-responsive" src="assets/clinic-logo.png" alt="Logo de la clínica" width="100%">
            </div>
          <?php endif; ?>
          <div class="col-md-9">
            <div class="form-group">
              <div class="col-lg-6">
                <label for="inputEmail1" class="control-label">Nombre:</label>
                <input type="text" name="configuration[name]" class="form-control" value="<?php echo $configuration['name']->value ?>">
              </div>
              <div class="col-lg-6">
                <label for="inputEmail1" class="control-label">Dirección:</label>
                <input type="text" name="configuration[address]" class="form-control" value="<?php echo $configuration['address']->value ?>">
              </div>
              <div class="col-lg-6">
                <label for="inputEmail1" class="control-label">Teléfono:</label>
                <input type="phone" class="form-control" value="<?php echo $configuration['phone']->value ?>" name="configuration[phone]" class="form-control">
              </div>
              <div class="col-lg-6">
                <label for="inputEmail1" class="control-label">Correo:</label>
                <input type="email" class="form-control" value="<?php echo $configuration['email']->value ?>" name="configuration[email]" class="form-control">
              </div>
              <div class="col-lg-6">
                <label for="inputEmail1" class="control-label">Nuevo logo (Formato .png):</label>
                <input type="file" name="logo" class="form-control" accept="image/jpeg" <?php echo (isset($configuration['logo']->value)) ? "" : "required" ?>>
              </div>
              <div class="col-lg-6">
                <label for="inputEmail1" class="control-label">Hora inicio de agenda:</label>
                <input type="time" class="form-control" value="<?php echo $configuration['calendar_start_hour']->value ?>" name="configuration[calendar_start_hour]" class="form-control" required>
              </div>
              <div class="col-lg-6">
                <label for="inputEmail1" class="control-label">Hora fin de agenda:</label>
                <input type="time" class="form-control" value="<?php echo $configuration['calendar_end_hour']->value ?>" name="configuration[calendar_end_hour]" class="form-control" required>
              </div>
              <div class="col-lg-6">
                <br>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="activeCardCommission" name="configuration[active_card_commission]" value="1" <?php echo ($configuration['active_card_commission']->value == 1) ? "checked" : "" ?> onclick="selectedCardComission()">
                    Incluir comisión por cada cobro con tarjeta.
                  </label>
                </div>
              </div>
              <div class="col-lg-6" id="divCardCommissionValue" <?php echo ($configuration['active_card_commission']->value == 1) ? '' : "style='display: none;'"; ?> <label for="inputEmail1" class="control-label">Cantidad cobrada por cada transacción de tarjeta</label>
                <div class="input-group">
                  <span class="input-group-addon">$</span>
                  <input type="text" name="configuration[card_commission_value]" class="form-control" value="<?php echo $configuration['card_commission_value']->value ?>">
                </div>
              </div>
              <hr>
              <div class="col-lg-6">
                <br>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="configuration[notifications_active_email_reservations]" value="1" <?php echo ($configuration['notifications_active_email_reservations']->value == 1) ? "checked" : "" ?>>
                    Activar recordatorios automáticos de citas por correo
                  </label>
                </div>
              </div>
              <div class="col-lg-6">
                <br>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="configuration[notifications_active_sms_reservations]" value="1" <?php echo ($configuration['notifications_active_sms_reservations']->value == 1) ? "checked" : "" ?>>
                    Activar recordatorios automáticos de citas por SMS
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-lg-2 pull-right">
                <button type="submit" class="btn btn-primary">Actualizar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
<script type="text/javascript">
  $(document).ready(function() {});

  function selectedCardComission() {
    if ($("#activeCardCommission").is(':checked')) $("#divCardCommissionValue").show();
    else $("#divCardCommissionValue").hide();
  }
</script>