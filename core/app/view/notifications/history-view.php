<?php
$actualDate = date('Y-m-d');
$patientId = (isset($_GET["patientId"])) ? $_GET["patientId"] : null;
$user = UserData::getLoggedIn();
$userType = $user->user_type;


$configuration = ConfigurationData::getAll();
$availableData = NotificationData::getAvailableSms();
$patient = PatientData::getById($patientId);

$emailNotifications = NotificationData::getAllByPatientType($patientId, 1);
$smsNotifications = NotificationData::getAllByPatientType($patientId, 2);
$whatsappNotifications = NotificationData::getAllByPatientType($patientId, 3);
$resumeNotifications = NotificationData::getResumeByPatient($patientId);

$patientImage = ($patient->image) ? "storage_data/patients/" . $patient->image : "../../../assets/default_user.jpg";
$clinicImage = (file_exists("assets/clinic-logo.png")) ? "../../../assets/clinic-logo.png" : "../../../assets/default_user.jpg";
$clinicName = $configuration["name"]->value;
?>
<div class="callout callout-<?php echo $availableData['class'] ?>">
  <h4>SMS Disponibles: <?php echo $availableData['total'] ?></h4>
  <p>Disponible para <?php echo $availableData['days'] ?> días</p>
</div>
<div class="row">
  <div class="col-md-12">
    <h1>Historial de notificaciones</h1>
    <h4> <?php echo $patient->name ?></h4>
  </div>
</div>
<br>
<div class="row">
  <div class="col-md-4">

    <div class="box box-primary direct-chat direct-chat-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Historial de correos</h3>
        <div class="box-tools pull-right">
          <span data-toggle="tooltip" title="" class="badge bg-light-blue" data-original-title="Mensajes totales"><?php echo $resumeNotifications["email"] ?></span>
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
        </div>
      </div>

      <div class="box-body">
        <div class="direct-chat-messages">
          <?php foreach ($emailNotifications as $emailNotification) :  ?>
            <div class="direct-chat-msg right">
              <div class="direct-chat-info clearfix">
                <span class="direct-chat-name pull-right"><?php echo $clinicName ?></span>
                <span class="direct-chat-timestamp pull-left"><?php echo $emailNotification->date_chat_format ?></span>
              </div>

              <img class="direct-chat-img" src="<?php echo $clinicImage ?>" alt="Clínica imagen">
              <div class="direct-chat-text">
                <?php echo $emailNotification->message ?>
              </div>

            </div>
          <?php endforeach; ?>

        </div>
      </div>

      <!--<div class="box-footer">
        <form action="#" method="post">
          <div class="input-group">
            <input type="text" name="message" placeholder="Escribe el mensaje ..." class="form-control">
            <span class="input-group-btn">
              <button type="submit" class="btn btn-primary btn-flat">Enviar</button>
            </span>
          </div>
        </form>
      </div>-->

    </div>

  </div>

  <div class="col-md-4">

    <div class="box box-success direct-chat direct-chat-success">
      <div class="box-header with-border">
        <h3 class="box-title">Historial SMS</h3>
        <div class="box-tools pull-right">
          <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="Mensajes totales"><?php echo $resumeNotifications["sms"] ?></span>
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
        </div>
      </div>

      <div class="box-body">

        <div class="direct-chat-messages">
          <?php foreach ($smsNotifications as $smsNotification) :  ?>

            <?php if ($smsNotification->direction_id == 1) : //Mensajes enviados a los pacientes
            ?>
              <div class="direct-chat-msg right">
                <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-right"><?php echo $clinicName ?></span>
                  <span class="direct-chat-timestamp pull-left"><?php echo $smsNotification->date_chat_format ?></span>
                </div>
                <img class="direct-chat-img" src="<?php echo $clinicImage ?>" alt="Clínica">
                <div class="direct-chat-text">
                  <?php echo $smsNotification->message ?>
                </div>
              </div>
            <?php else : //Mensajes enviados/respondidos por el paciente
            ?>
              <div class="direct-chat-msg">
                <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-left"><?php echo $patient->name ?></span>
                  <span class="direct-chat-timestamp pull-right"><?php echo $smsNotification->date_chat_format ?></span>
                </div>
                <img class="direct-chat-img" src="<?php echo $patientImage ?>" alt="Paciente">
                <div class="direct-chat-text">
                  <?php echo $smsNotification->message ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>

        </div>

      </div>
      <!--
      <div class="box-footer">
        <form action="#" method="post">
          <div class="input-group">
            <input type="text" name="message" placeholder="Escribir mensaje ..." class="form-control">
            <span class="input-group-btn">
              <button type="submit" class="btn btn-success btn-flat">Enviar</button>
            </span>
          </div>
        </form>
      </div>
          -->

    </div>

  </div>

  <div class="col-md-4">

    <div class="box box-success direct-chat direct-chat-success">
      <div class="box-header with-border">
        <h3 class="box-title">Historial Whatsapp</h3>
        <div class="box-tools pull-right">
          <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="Mensajes totales"><?php echo $resumeNotifications["whatsapp"] ?></span>
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
        </div>
      </div>

      <div class="box-body">

        <div class="direct-chat-messages">
          <?php foreach ($whatsappNotifications as $whatsappNotification) :  ?>

            <?php if ($whatsappNotification->direction_id == 1) : //Mensajes enviados a los pacientes
            ?>
              <div class="direct-chat-msg right">
                <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-right"><?php echo $clinicName ?></span>
                  <span class="direct-chat-timestamp pull-left"><?php echo $whatsappNotification->date_chat_format ?></span>
                </div>
                <img class="direct-chat-img" src="<?php echo $clinicImage ?>" alt="Clínica">
                <div class="direct-chat-text">
                  <?php echo $whatsappNotification->message ?>
                </div>
              </div>
            <?php else : //Mensajes enviados/respondidos por el paciente
            ?>
              <div class="direct-chat-msg">
                <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-left"><?php echo $patient->name ?></span>
                  <span class="direct-chat-timestamp pull-right"><?php echo $whatsappNotification->date_chat_format ?></span>
                </div>
                <img class="direct-chat-img" src="<?php echo $patientImage ?>" alt="Paciente">
                <div class="direct-chat-text">
                  <?php echo $whatsappNotification->message ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>

        </div>

      </div>
      <!--
      <div class="box-footer">
        <form action="#" method="post">
          <div class="input-group">
            <input type="text" name="message" placeholder="Escribir mensaje ..." class="form-control">
            <span class="input-group-btn">
              <button type="submit" class="btn btn-success btn-flat">Enviar</button>
            </span>
          </div>
        </form>
      </div>
          -->

    </div>

  </div>

</div>