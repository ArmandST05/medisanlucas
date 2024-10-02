<!DOCTYPE html>
<?php
$user = UserData::getLoggedIn();
$availableSmsData = NotificationData::getAvailableSms();
$availableSmsTotal = $availableSmsData["total"];
if ($user) {
  $userType = (isset($user)) ? $user->user_type : null;
  $user_name = (isset($user)) ? $user->username : null;
  $medic = MedicData::getByUserId($user->id);
}
$date = date("Y-m-d");
date_default_timezone_set('America/Mexico_City');
$configuration = ConfigurationData::getAll();
require_once 'vendor/autoload.php';

//NOTIFICACIONES CORREO ELECTRÓNICO
//Se realiza el envío de las notificaciones por correo electrónico si está el sistema configurado.
use PHPMailer\PHPMailer\PHPMailer;

$endDateNotifications = date("Y-m-d", strtotime(date("Y-m-d") . "+ " . ($configuration["notifications_default_previous_days_reservation"]->value) . " days"));
$startDateTimeNotifications = date("Y-m-d") . " 00:00:01";
$endDateTimeNotifications = $endDateNotifications . " 23:59:59";

if (isset($_GET["view"]) && ($_GET["view"] != "emails/new" && $_GET["view"] != "notifications/index") && $configuration["notifications_active_email_reservations"]->value == 1 && $configuration['email']->value) {
  $reservations = ReservationData::getBetweenDates($startDateTimeNotifications, $endDateTimeNotifications, 0, $reservationType = "patient", 1, 1);

  foreach ($reservations as $reservation) {
    $defaultMessage = NotificationData::getDefaultMessageByModuleType(1, 1);
    $mailReservation = new PHPMailer();
    $mailReservation->Host = $configuration['name']->value;
    $mailReservation->From = $configuration['email']->value;
    $mailReservation->FromName = $configuration['name']->value;
    $mailReservation->Subject = "Recordatorio de cita " . $configuration["name"]->value;
    $mailReservation->AddAddress($reservation->patient_email); //Destinatarios
    if (isset($configuration['notifications_email_file_path']->value)) {
      /*$varname = $_FILES['file']['name'];
      $vartemp = $_FILES['file']['tmp_name'];
      $mailReservation->AddAttachment($vartemp, $varname);*/
    }
    $body = "Buen día, nos comunicamos de " . $configuration["name"]->value . " para recordarte que tienes una cita el día " . $reservation->date_at_format . " con " . $reservation->medic_name . " en " . $configuration["address"]->value . ".<br>
    Si tienes algún comentario sobre tu asistencia a la cita, comunícate al " . $configuration["phone"]->value . "<br>Saludos<br><img src='assets/clinic-logo.png' rows='20'>";
    $mailReservation->Body = $body;
    $mailReservation->IsHTML(true);
    if ($mailReservation->Send()) {
      //Registrar notifación en el sistema
      $notification = new NotificationData();
      $notification->patient_id = $reservation->patient_id;
      $notification->reservation_id = $reservation->id;
      $notification->type_id = 1; //Correo
      $notification->direction_id = 1; //Enviado
      $notification->status_id = 1;
      $notification->module_id = 1; //Recordatorios
      $notification->receptor = $reservation->patient_email;
      $notification->message = $body;
      $notification->add();
    }
  }
}
?>
<html>

<head>
  <meta charset="UTF-8">
  <title>POWER DR</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <link rel="icon" href="assets/powerdr-icon.png">

  <!-- Jquery -->
  <script src="assets/jquery-2.1.1.min.js" type="text/javascript"></script>

  <!-- Bootstrap 3.3.4 -->
  <link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- Font Awesome Icons -->
  <link href="plugins/font-awesome/css/all.min.css" rel="stylesheet" type="text/css" />
  <!-- Theme style -->
  <link href="plugins/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
  <link href="plugins/dist/css/skins/skin-blue-light.min.css" rel="stylesheet" type="text/css" />
  <!--link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css"-->
  <link href="plugins/colorpicker/dist/css/bootstrap-colorpicker.css" rel="stylesheet" type="text/css">

  <!-- SweetAlert -->
  <link href="plugins/sweetalert/min.css" rel="stylesheet" type="text/css" />
  <script src="plugins/sweetalert/min.js"></script>

  <script type='text/javascript' src='plugins/nicedit/nicEdit.js'></script>
  <!--<?php if ($userType == "su" || $userType == "do") : ?>
    <script type='text/javascript' src='plugins/nicedit/nicEdit.js'></script>
    <script type='text/javascript'>
      bkLib.onDomLoaded(function() {
        nicEditors.allTextAreas()
      });
    </script>
  <?php endif; ?>-->

  <script src="plugins/jquery/jquery-2.1.4.min.js"></script>
  <script src="plugins/morris/raphael-min.js"></script>
  <script src="plugins/morris/morris.js"></script>
  <link rel="stylesheet" href="plugins/morris/morris.css">
  <link rel="stylesheet" href="plugins/morris/example.css">
  <script src="plugins/jspdf/jspdf.min.js"></script>
  <script src="plugins/jspdf/jspdf.plugin.autotable.js"></script>

  <?php if (isset($_GET["view"]) && $_GET["view"] == "sales") : ?>
    <script type="text/javascript" src="plugins/jsqrcode/llqrcode.js"></script>
    <script type="text/javascript" src="plugins/jsqrcode/webqr.js"></script>
  <?php endif; ?>

  <!-- Sweet Alert -->
  <script src="plugins/sweetalert/min.js"></script>
  <!--  Sweet Alert-->
  <!-- ColorPicker -->
  <script src="plugins/colorpicker/dist/js/bootstrap-colorpicker.js"></script>
  <!-- ColorPicker -->
  <!-- Select2 -->
  <link href="plugins/select2/select2.min.css" rel="stylesheet" />
  <script src="plugins/select2/select2.min.js"></script>
  <!-- Select2 -->
  <!-- CALENDAR-->
  <script src='assets/js/moment.min.js'></script>
  <script src='assets/js/jquery-ui.min.js'></script>

  <script src='node_modules/fullcalendar/index.global.min.js'></script>
  <script src='node_modules/@fullcalendar/core/locales-all.global.js'></script>
  <!-- CALENDAR -->

  <!--AUTOCOMPLETE JQUERY UI-->
  <!--<script src="https://code.jquery.com/jquery-3.6.0.js"></script>-->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>

<body onload='' class="<?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])) : ?>  skin-blue-light sidebar-mini <?php else : ?>login-page<?php endif; ?>">
  <div class="background">
    <div class="wrapper">
      <!-- Main Header -->
      <?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])) : ?>
        <header class="main-header">
          <!-- Logo -->
          <a href="./" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"> <img src="assets/clinic-logo.png" width="40"></span>
            <!-- logo for regular state and mobile devices -->
            <img src="assets/clinic-logo.png" width="100" height="40">
          </a>

          <!-- Header Navbar -->
          <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><i class="fas fa-bars"></i>
              <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
              <ul class="nav navbar-nav">

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                  <!-- Menu Toggle Button -->
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <!-- The user image in the navbar-->
                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                    <span class=""><i class="fas fa-user"></i> <?php echo $user_name ?> </span>
                  </a>
                </li>
                <li class="dropdown user user-menu">
                  <!-- Menu Toggle Button -->
                  <a href="./logout.php" class="dropdown-toggle">Salir <i class="fas fa-sign-out-alt"></i></a>
                </li>
                <!-- Control Sidebar Toggle Button -->
              </ul>
            </div>
          </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

          <!-- sidebar: style can be found in sidebar.less -->
          <section class="sidebar">

            <!-- Sidebar Menu  ADMINISTRADOR-->
            <ul class="sidebar-menu">

              <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "su")) : ?>
                <meta charset="UTF-8">
                <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
                <!--li><a href="./?view=sell"><i class='fa fa-usd'></i> <span>Vender</span></a></li-->
                <li><a href="./?view=patients/index"><i class='fas fa-user-alt'></i> <span>Pacientes</span></a></li>
                <li class="treeview">
                  <a href="#"><i class='fa fa-diagnoses'></i> <span>SUIVE</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=suive/index">Formatos</a></li>
                    <li><a href="./?view=suive/diagnostic-report">Reporte Diagnósticos</span></a></li>
                  </ul>
                </li>

                <li class="treeview">
                  <a href="#"><i class='fa fa-th-list'></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=diagnostics/index">Diagnósticos</a></li>
                    <li><a href="./?view=medic-categories/index">Especialidades Médico</span></a></li>
                    <li><a href="./?view=laboratories/index">Laboratorios/Consultorios</a></li>
                    <li><a href="./?view=medics/index">Médicos</a></li>
                    <li><a href="./?view=medicines/index">Medicamentos</a></li>
                  </ul>
                </li>

                <li class="treeview">
                  <a href="#"><i class="fas fa-pills"></i> <span>Productos e insumos</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=products/index">Productos</a></li>
                    <li><a href="./?view=supplies/index">Insumos</a></li>
                  </ul>
                </li>

                <li class="treeview">
                  <a href="#"><i class='fas fa-money-bill-alt'></i> <span>Gastos</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=expense-categories/index">Categoría gastos</a></li>
                    <li><a href="./?view=expense-concepts/index">Conceptos gastos</a></li>
                    <li><a href="./?view=expenses/index&limit">Gastos</a></li>
                  </ul>
                </li>

                <li class="treeview">
                  <a href="#"><i class='fa fa-database'></i> <span>Ingresos</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=income-concepts/index">Conceptos</a></li>
                    <li><a href="./?view=sales/index">Ingresos</a></li>
                    <!--<li><a href="./?view=sales/receivable-accounts">Cuentas por cobrar</a></li>-->
                    <li><a href="./?view=cashier-balance/index">Cortes</a></li>
                    <li><a href="./?view=cashier-balance/index-personal">Cortes Personal</a></li>
                  </ul>
                </li>

                <li class="treeview">
                  <a href="#"><i class='fas fa-boxes'></i> <span>Inventario</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=inventory/index-products">Inventario Medicamento</a></li>
                    <li><a href="./?view=inventory/index-supplies">Inventario Insumos</a></li>
                    <li><a href="./?view=inventory/index-outputs">Salidas</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#"><i class='fas fa-file-alt'></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=reports/inventory&ed=<?php echo $date ?>">Reporte inventario medicamentos</a></li>
                    <li><a href="./?view=reports/inventory-supplies&ed=<?php echo $date ?>">Reporte inventario insumos</a></li>
                    <li><a href="./?view=reports/product-sales&ed=<?php echo $date ?>">Reporte ventas producto</a></li>
                    <!--
                    <li><a href="./?view=reports/incomeexpenses">Reporte Ingresos y Egresos</a></li>
                    <li><a href="./?view=reportsSellCir">Cirugías por cobrar y cobradas</a></li>
                    <li><a href="./?view=reportsSell">Cuentas por cobrar y cobradas</a></li>
                    <li><a href="./?view=reportsExp">Cuentas por pagar y pagadas</a></li>
                    <li><a href="./?view=utilidad">Margen de utilidad</a></li>
                    <li><a href="./?view=reports/invoices">Facturado</a></li>
                    <li><a href="./?view=reports/noinvoice">No facturado</a></li>
              -->
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#"><i class="fas fa-envelope"></i> <span>Notificaciones</span> <i class="fas fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=notifications/index">Notificación Citas</a></li>
                    <li><a href="./?view=emails/new">Felicitación correo</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#"><i class='fas fa-cog'></i> <span>Configuración</span> <i class="fas fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=configuration/edit-clinic-profile">Perfil Clínica</a></li>
                    <?php if (isset($medic)) : ?>
                      <li><a href="./?view=configuration/edit-medic-profile">Perfil Médico</a></li>
                    <?php endif; ?>
                    <li><a href="./?view=users/index">Usuarios</a></li>
                  </ul>
                </li>
            </ul>
          <?php endif; ?>

          <!-- MÉDICO GENERAL ASISTENTE (SUB-ADMINISTRADOR)-->
          <ul class="sidebar-menu">
            <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "mg")) : ?>
              <meta charset="UTF-8">
              <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
              <li><a href="./?view=patients/index"><i class='fas fa-user-alt'></i> <span>Pacientes</span></a></li>
          </ul>
        <?php endif; ?>

        <!-- Doctor-->
        <ul class="sidebar-menu">

          <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "do")) : ?>
            <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
            <!--li><a href="./?view=sell"><i class='fa fa-usd'></i> <span>Vender</span></a></li-->
            <li><a href="./?view=patients/index"><i class='fas fa-user-alt'></i> <span>Pacientes</span></a></li>
            <li><a href="./?view=configuration/edit-medic-profile"><i class='fas fa-cog'></i> <span>Perfil</span></a></li>
          <?php endif; ?>

          <!-- Recepcionista-->
          <ul class="sidebar-menu">

            <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "r")) : ?>
              <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
              <!--li><a href="./?view=sell"><i class='fa fa-usd'></i> <span>Vender</span></a></li-->
              <li><a href="./?view=patients/index"><i class='fas fa-user-alt'></i> <span>Pacientes</span></a></li>

              <!--<li class="treeview">
                <a href="#"><i class='fas fa-money-bill-alt'></i> <span>Gastos</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="./?view=expenses/new">Agregar gasto</a></li>
                  <li><a href="./?view=expenses/index&limit">Ver gastos</a></li>
                </ul>
              </li>-->

              <li class="treeview">
                <a href="#"><i class='fa fa-database'></i> <span>Ingresos</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="./?view=sales/index">Ingresos</a></li>
                  <li><a href="./?view=cashier-balance/index-personal">Cortes Personal</a></li>
                </ul>
              </li>
              <li class="treeview">
                <a href="#"><i class="fas fa-envelope"></i> <span>Notificaciones</span> <i class="fas fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="./?view=notifications/index">Notificación Citas</a></li>
                </ul>
              </li>
            <?php endif; ?>

            <!-- Enfermera-->
            <ul class="sidebar-menu">

              <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "a")) : ?>
                <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
                <li><a href="./?view=patients/index"><i class='fas fa-user-alt'></i> <span>Pacientes</span></a></li>

                <li class="treeview">
                  <a href="#"><i class='fas fa-boxes'></i> <span>Inventario</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=inventory/index-products">Inventario Medicamento</a></li>
                    <li><a href="./?view=reports/inventory&ed=<?php echo $date ?>">Reporte inventario medicamentos</a></li>
                    <li><a href="./?view=reports/inventory-supplies&ed=<?php echo $date ?>">Reporte inventario insumos</a></li>
                  </ul>
                </li>

              <?php endif; ?>

              <!-- Enfermera-->
              <ul class="sidebar-menu">
                <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "au")) : ?>
                  <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
                  <li><a href="./index.php?view=reports/invoices"><i class='fa fa-file-text-o'></i> <span>Ventas y Compras</span></a></li>
                <?php endif; ?>
          </section>
        </aside>
      <?php endif; ?>

      <!-- Content Wrapper. Contains page content -->
      <?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])) : ?>
        <div class="content-wrapper">
          <div class="content">
            <?php View::load("index"); ?>
          </div>
        </div><!-- /.content-wrapper -->

        <footer class="main-footer">
          <div class="pull-right hidden-xs">
          </div>
          Copyright © <a href="https://www.v2technoconsulting.com" target="_blank">Techno Consulting</a> <!-- Credit: www.templatemo.com -->
        </footer>
      <?php else : ?>
        <style>
          body::after {
            content: "";
            background-image: url("assets/background.png") !important;
            background-size: cover !important;
            opacity: 0.2;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            position: absolute;
            z-index: -1;
          }
        </style>
        <div class="login-box">
          <div class="login-box-body">
            <form action="./?action=processLogin" method="post">

              <!--<span class="label label-primary"></span>-->
              <div class="form-group">
                <img src="assets/powerdr-logo.png" width="300px;">
              </div>
              <div class="form-group has-feedback">
                <input type="text" name="username" required class="form-control" placeholder="Usuario" />
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
              </div>
              <div class="form-group has-feedback">
                <input type="password" name="password" required class="form-control" placeholder="Contraseña" />
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn-primary btn-block btn-flat">Acceder</button>
                </div><!-- /.col -->
              </div>
            </form>
          </div><!-- /.login-box-body -->
        </div><!-- /.login-box -->
      <?php endif; ?>

    </div><!-- ./wrapper -->
  </div>
  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery 2.1.4 -->
  <!-- Bootstrap 3.3.2 JS -->
  <script src="plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="plugins/dist/js/app.min.js" type="text/javascript"></script>

  <script src="plugins/datatables/jquery.dataTables.js"></script>
  <script src="plugins/datatables/dataTables.bootstrap.js"></script>

  <!-- Locales for moment.js-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.4/locale/es.js"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.10.15/sorting/datetime-moment.js"></script>

  <!--script src="plugins/datatables/jquery.dataTables.min.js"></script>
      <script src="plugins/datatables/dataTables.bootstrap.min.js"></script-->
  <!-- Optionally, you can add Slimscroll and FastClick plugins.
            Both of these plugins are recommended to enhance the
            user experience. Slimscroll is required when using the
            fixed layout. -->

  <script type="text/javascript">
    $(document).ready(function() {
      //Sweet Alert
      /*Swal.fire({
          title: '¡Atención!',
          text: 'Estimado usuario, tu sistema presenta un saldo vencido. Te invitamos a regularizarlo a la brevedad posible.',
          icon: 'error',
          confirmButtonText: 'Aceptar'
      });*/
      if ("<?php echo (isset($_GET["view"]) && $_GET["view"] != "notifications/index") ?>" == true && <?php echo $configuration["notifications_active_sms_reservations"]->value ?> == 1 && "<?php echo $availableSmsTotal ?>" > 0) {
        sendSmsNotificationsAll();
      }
    });

    const removeAccents = (str) => {
      return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    function sendSmsNotificationsAll() {
      var username = '<?php echo $configuration["notifications_sms_username"]->value ?>';
      var authenticationToken = '<?php echo $configuration["notifications_sms_authentication_token"]->value ?>';
      var reservations = <?php echo json_encode(ReservationData::getBetweenDates($startDateTimeNotifications, $endDateTimeNotifications, 0, $reservationType = "patient", 2, 2)) ?>;

      var availableSmsData = <?php echo $availableSmsTotal; ?>;

      $.each(reservations, function(i, reservation) {
        if (availableSmsData > 0) {
          var cellphone = reservation["patient_phone"];
          var reservationId = reservation["id"];
          var sendedMessage = false;

          var message = "Buen dia, te recordamos que tienes una cita agendada el dia " + reservation["date_at_format"] + " ";
          message += "<?php echo $configuration["name"]->value ?>" + " Tel: " + "<?php echo $configuration["phone"]->value ?>";
          message = message.replace(/\//g, "-");
          messageContent = removeAccents(message);

          if (cellphone.trim().length == 10) {
            cellphone = 52 + cellphone;
            //Envío de SMS https://docs-latam.wavy.global/documentacion-tecnica/api-integraciones/sms-api
            $.ajax({
              type: "GET",
              statusCode: {
                403: function(xhr) {
                  //alertify.error("Ha ocurrido un error en el envío");
                }
              },
              url: "https://api-messaging.wavy.global/v1/send-sms",
              headers: {
                'Access-Control-Allow-Origin': '*'
              },
              dataType: "jsonp",
              contentType: "application/json",
              data: {
                username: username,
                authenticationToken: authenticationToken,
                destination: cellphone,
                messageText: messageContent
              },
              success: function(data) {
                sendedMessage = true;
                //alertify.success("Mensaje Enviado");
              },
              error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                  sendedMessage = true;
                  //alertify.success("Mensaje Enviado");
                } else {
                  //alertify.error("Ha ocurrido un error en el envío");
                }
              },
              complete: function(data) {
                if (sendedMessage == true) {
                  $.ajax({
                    type: "POST",
                    url: "./?action=notifications/add-sms",
                    data: {
                      reservationId: reservationId,
                      receptor: cellphone,
                      message: messageContent
                    },
                    success: function(data) {

                    },
                    error: function() {},
                    complete: function(data) {}
                  })
                }
              }
            })
          }
          availableSmsData = availableSmsData - 1;
        }
      });
    }
  </script>

</html>
