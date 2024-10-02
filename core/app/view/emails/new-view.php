<?php
require(dirname(__FILE__) . "/../../../../plugins/phpmailer/Exception.php");
require(dirname(__FILE__) . "/../../../../plugins/phpmailer/PHPMailer.php");
require(dirname(__FILE__) . "/../../../../plugins/phpmailer/SMTP.php");

use PHPMailer\PHPMailer\PHPMailer;

$configuration = ConfigurationData::getAll();
//Consulta los pacientes que cumplen años en este mes
$date = date("m-d");
$patients = PatientData::getEmailsByPatientBirthdayMonth($date);

$patientEmails = "";
foreach ($patients as $patient) {
  $patientEmails .= trim($patient->email) . ",";
}

$msg = "";
$action = isset($_POST['action'])  ? $_POST['action'] : null;
if ($action == "send") {
  $varname = $_FILES['archivo']['name'];
  $vartemp = $_FILES['archivo']['tmp_name'];

  $mail = new PHPMailer();
  $mail->Host = $configuration['name']->value;
  $mail->From = $configuration['email']->value;
  $mail->FromName = $configuration['name']->value;
  $mail->Subject = $_POST['subject'];
  $mail->AddAddress($_POST['destination']);
  if ($varname != "") {
    $mail->AddAttachment($vartemp, $varname);
  }
  $body = "<img src='../../../../assets/clinic-logo.png' rows='20'>";
  //$body.= "<i>Enviado por ---"."<br>";
  //$mail->AddEmbeddedImage('assets/clinic-logo.png', $type = "assets/clinic-logo.png");
  $mail->Body = $body;
  //$mail->AddAttachment("assets/clinic-log.png");
  $mail->IsHTML(true);
  $mail->Send();
  $msg = "Mensaje enviado correctamente";
}
?>
<div class="row">
  <div class="col-md-12">
    <h1 class="title">Felicitación</h1>
    <?php if ($configuration['email'] != '') : ?>
      <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="callout callout-default">
            <h4>Información</h4>
            <p>En el campo "Para" aparecen por defecto los correos de los pacientes que cumplen años este mes para enviarles una felicitación.</p>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-1 control-label">Para:</label>
            <div class="col-md-10">
              <input type="text" name="destination" id="destination" value="<?php echo $patientEmails ?>" class="form-control" placeholder="Para" autofocus autocomplete="off">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-1 control-label">Asunto:</label>
            <div class="col-md-10">
              <input type="text" name="subject" id="subject" class="form-control" placeholder="Asunto" value="<?php echo $configuration['name']->value ?>" autofocus autocomplete="off">
            </div>
          </div>
        </div>
        <div class="row">
          <label for="inputEmail1" class="col-lg-1 control-label">Archivo:</label>
          <div class="col-md-10">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-paperclip"></i></span>
              <input type="file" name="archivo" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-2 pull-right">
            <button type="submit" name="btsend" class="btn btn-warning btn-sm" onclick="message();"><i class="fa fa-envelope"></i> Enviar</button>
            <input type="hidden" name="action" value="send">
            <input type="hidden" name="view" value="emails/new">
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="login-logo ">
              <!--  <img src="assets/felicitacion.jpg" name="mensaje" id="mensaje"  ></img>-->
            </div>
          </div>
        </div>
      </form>
    <?php else : ?>
      <div class="row">
        <div class="callout callout-info">
          <h4>No tienes registrado un correo de la clínica</h4>
          <p>Ve a <b>Configuración/Perfil General</b> y agrega tu correo empresarial para enviar mensajes a tus clientes.</p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<script src="assets/jquery.searchable-1.0.0.min.js"></script>
<!--script language="JavaScript" type="text/javascript" src="assets/ajax.js"></script-->
<script type="text/javascript">
  $(document).ready(function() {

  });

  function message() {
    alert('Enviado Correctamente');
  }
</script>