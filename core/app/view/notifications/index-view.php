<?php
$actualDate = date('Y-m-d');
$startDate = (isset($_GET["sd"])) ? $_GET["sd"] : $actualDate;
$endDate = (isset($_GET["ed"])) ? $_GET["ed"] : date("Y-m-d", strtotime($actualDate . "+ 3 days"));
$user = UserData::getLoggedIn();
$userType = $user->user_type;

$startDateTime = $startDate . " 00:00:01";
$endDateTime = $endDate . " 23:59:59";

$configuration = ConfigurationData::getAll();
$reservations = ReservationData::getBetweenDates($startDateTime, $endDateTime, 0, "patient", 0, 0);

$availableSmsData = NotificationData::getAvailableSms();
$availableSmsTotal = $availableSmsData["total"];
?>
<div class="callout callout-<?php echo $availableSmsData['class'] ?>">
  <h4>SMS Disponibles: <?php echo $availableSmsData['total'] ?></h4>
  <p>Disponible para <?php echo $availableSmsData['days'] ?> días</p>
</div>
<div class="callout callout-default">
  <p><label class="text-black"><?php echo ($configuration["notifications_active_email_reservations"]->value == 1) ? "Tienes " : "No tienes " ?> activas las notificaciones automáticas por correo electrónico.</label></p>
  <p><label class="text-black"><?php echo ($configuration["notifications_active_sms_reservations"]->value == 1) ? "Tienes " : "No tienes " ?> activas las notificaciones automáticas por SMS.</label></p>
  <?php if ($userType == "su") : ?>
    <p><a class="text-black" href="index.php?view=configuration/edit-clinic-profile"><i class="fas fa-pencil-alt"></i> Editar configuración</a></p>
  <?php endif; ?>
</div>
<div class="row">
  <div class="col-md-12">
    <h1>Notificaciones citas</h1>
  </div>
</div>
<br>
<div class="row">
  <div class="col-md-12">
    <form>
      <input type="hidden" name="view" value="notifications/index">
      <div class="row">
        <div class="col-md-3">
          <input type="date" name="sd" value="<?php echo $startDate ?>" class="form-control">
        </div>
        <div class="col-md-3">
          <input type="date" name="ed" value="<?php echo $endDate ?>" class="form-control">
        </div>

        <div class="col-md-2">
          <input type="submit" class="btn btn-success btn-block" value="Procesar">
        </div>

        <?php if ($userType == "su") : ?>
          <div class="col-md-2">
            <input type="submit" class="btn btn-primary btn-block" value="Exportar Excel" id="btnExport">
          </div>
        <?php endif; ?>
      </div>

    </form>

  </div>
</div>
<br>
<hr>
<div class="row">
  <div class="col-md-12">
    <div class="clearfix"></div>

    <table id="datosexcel" border='1' class="table table-bordered table-hover">
      <thead bgcolor="#eeeeee" align="center">
        <th>Fecha cita</th>
        <th>Paciente</th>
        <th>Médico</th>
        <th>Motivo</th>
        <th>Teléfono</th>
        <th>Correo</th>
        <th>Notificación Correo</i></th>
        <th>Notificación SMS <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Requieres contratar un plan con tu administrador"></th>
        <th>Notificación Whatsapp <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Abre Whatsapp en tu computadora/celular para poder realizarlo"></th>
        <th>Acciones</th>
      </thead>

      <?php foreach ($reservations as $reservation) :
        $medic = $reservation->getMedic();
        $class = "success";
        $notificationsData = NotificationData::getResumeByReservation($reservation->id);
      ?>
        <tr class="<?php echo $notificationsData['class'] ?>">
          <td><?php echo $reservation->date_at_format ?></td>
          <td><?php echo $reservation->patient_name ?></td>
          <td><?php echo $medic->name ?></td>
          <td><?php echo $reservation->reason ?></td>
          <td><?php echo $reservation->patient_phone ?></td>
          <td><?php echo $reservation->patient_email ?></td>
          <td><?php if ($notificationsData['email'] > 0) : ?>
              <span class="label label-primary"><?php echo $notificationsData['email'] ?> CORREO Enviado</span>
            <?php endif; ?>
          </td>
          <td><?php if ($notificationsData['sms'] > 0) : ?>
              <span class="label label-success"><?php echo $notificationsData['sms'] ?> SMS Enviado</span>
            <?php endif; ?>
          </td>
          <td><?php if ($notificationsData['whatsapp'] > 0) : ?>
              <span class="label label-success"><?php echo $notificationsData['whatsapp'] ?> Whatsapp Enviado</span>
            <?php endif; ?>
          </td>
          <td>
            <button type="button" class="btn btn-success btn-xs" onclick="sendWhatsappNotification(`<?php echo $reservation->id ?>`,`<?php echo $reservation->patient_phone ?>`,`<?php echo $reservation->date_at_format ?>`,`<?php echo $medic->name ?>`,`<?php echo $reservation->patient_name ?>`)"><i class="fas fa-phone"></i>
              Notificar por Whatsapp
            </button><br>
            <button type="button" class="btn btn-default btn-xs" onclick="showModalEmailNotification(`<?php echo $reservation->id ?>`,`<?php echo $reservation->patient_email ?>`,`<?php echo $reservation->date_at_format ?>`,`<?php echo $medic->name ?>`,`<?php echo $reservation->patient_name ?>`)"><i class="fas fa-envelope-open-text"></i><i class="fas fa-at"></i>
              Notificar por correo
            </button>
            <?php if ($availableSmsTotal > 0) : ?>
              <button type="button" class="btn btn-default btn-xs" onclick="showModalSmsNotification(`<?php echo $reservation->id ?>`,`<?php echo $reservation->patient_phone ?>`,`<?php echo $reservation->date_at_format ?>`,`<?php echo $medic->name ?>`,`<?php echo $reservation->patient_name ?>`)"><i class="fas fa-envelope-open-text"></i></i>
                Notificar por SMS
              </button>
            <?php endif; ?>
            <br>
            <a target="_blank" href="index.php?view=notifications/history&patientId=<?php echo $reservation->patient_id ?>" class="btn btn-xs btn-warning"><i class="fas fa-comment"></i> Historial</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<!--MODAL NOTIFICACIÓN CORREO CITA-->
<div class="modal fade in" id="modalEmailNotification">
  <div class="modal-dialog">
    <div class="modal-content">
      <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=notifications/add-email" role="form">
        <div class="modal-header">
          <button type="button" class="close" onclick="hideModalEmailNotification()">×</span></button>
          <h4 class="modal-title">Notificación cita por correo</h4>
        </div>
        <div class="modal-body">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="exampleInputEmail1">Paciente</label>
                  <input type="text" class="form-control" placeholder="Paciente" id="patientNameEmail" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Correo de clínica</label>
                  <input type="text" class="form-control" placeholder="Correo electrónico clínica" value="<?php echo trim($configuration['email']->value) ?>" readonly required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Correo de paciente</label>
                  <input type="text" class="form-control" placeholder="Correo electrónico del paciente" id="receptorEmail" name="receptor" readonly required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="exampleInputEmail1">Asunto</label>
                  <input type="text" class="form-control" placeholder="Asunto" id="subjectEmail" name="subject" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="exampleInputEmail1">Contenido del correo</label>
                  <textarea class="form-control" id="messageEmail" name="message" rows="7" required></textarea>
                </div>
              </div>
            </div>
            <br>
            <input type="hidden" name="reservationId" id="reservationIdEmail" ?>
            <input type="hidden" name="startDate" value="<?php echo $startDate ?>">
            <input type="hidden" name="endDate" value="<?php echo $endDate ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal" onclick="hideModalEmailNotification()">Cerrar</button>
          <button type="submit" class="btn btn-primary">Enviar correo</button>
        </div>
      </form>
    </div>

  </div>

</div>

<!--MODAL NOTIFICACIÓN SMS CITA-->
<div class="modal fade in" id="modalSmsNotification">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick="hideModalSmsNotification()">×</span></button>
        <h4 class="modal-title">Notificación cita por sms</h4>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="exampleInputEmail1">Paciente</label>
                <input type="text" class="form-control" placeholder="Paciente" id="patientNameSms" readonly>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="exampleInputEmail1">Teléfono de paciente</label>
                <input type="text" class="form-control" placeholder="Teléfono del paciente" id="receptorSms" name="receptor" readonly required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="exampleInputEmail1">Contenido del sms</label>
                <textarea class="form-control" id="messageSms" name="message" rows="7" required></textarea>
              </div>
            </div>
          </div>
          <br>
          <input type="hidden" name="reservationId" id="reservationIdSms" ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal" onclick="hideModalSmsNotification()">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="sendSmsNotifications()">Enviar SMS</button>
      </div>
    </div>

  </div>

</div>

<script src="assets/jquery.btechco.excelexport.js"></script>
<script src="assets/jquery.base64.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    var dataTable = $('#datosexcel').DataTable({
      "language": {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix": "",
        "sSearch": "Buscar:",
        "sUrl": "",
        "sInfoThousands": ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
          "sFirst": "Primero",
          "sLast": "Último",
          "sNext": "Siguiente",
          "sPrevious": "Anterior"
        },
        "oAria": {
          "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      },
      "ordering": true,
      "processing": true,
    });

    $("#btnExport").click(function(e) {
      $("#datosexcel").btechco_excelexport({
        containerid: "datosexcel",
        datatype: $datatype.Table,
        filename: 'Cortes Personal'
      });
    });

  });

  function showModalEmailNotification(reservationId = "", receptor = "", dateAtFormat = "", medicName = "", patientName = "") {
    $("#modalEmailNotification").css("display", "block");
    $("#reservationIdEmail").val(reservationId);
    $("#receptorEmail").val(receptor);
    $("#patientNameEmail").val(patientName);
    $("#subjectEmail").val("Recordatorio de cita " + "<?php echo $configuration['name']->value ?>");

    var message = "Buen día, nos comunicamos de " + "<?php echo $configuration["name"]->value ?>" + " para recordarte que tienes una cita el día " + dateAtFormat + " con " + medicName + " en " + "<?php echo $configuration["address"]->value ?>" + "\n";
    message += "Si tienes algún comentario sobre tu asistencia a la cita, comunícate al " + "<?php echo $configuration["phone"]->value ?>" + "\n Saludos";
    $("#messageEmail").text(message);
  }

  function hideModalEmailNotification() {
    $("#modalEmailNotification").css("display", "none");
  }

  function showModalSmsNotification(reservationId = "", receptor = "", dateAtFormat = "", medicName = "", patientName = "") {
    $("#modalSmsNotification").css("display", "block");
    $("#reservationIdSms").val(reservationId);
    $("#receptorSms").val(receptor);
    $("#patientNameSms").val(patientName);

    var message = "Buen dia, te recordamos que tienes una cita agendada el dia " + dateAtFormat + " ";
    message += "<?php echo $configuration["name"]->value ?>" + " Tel: " + "<?php echo $configuration["phone"]->value ?>";
    message = message.replace(/\//g, "-");
    message = removeAccents(message);

    $("#messageSms").text(message);
  }

  function hideModalSmsNotification() {
    $("#modalSmsNotification").css("display", "none");
  }

  if (<?php echo $availableSmsTotal ?> > 0) {
    function sendSmsNotifications() {
      var cellphone = $("#receptorSms").val();
      var messageContent = $("#messageSms").val();
      var reservationId = $("#reservationIdSms").val();
      var sendedMessage = false;

      if (cellphone.trim().length > 0) {
        if (cellphone.length != 10) alertify.success("El teléfono debe tener 10 dígitos");
        else {
          cellphone = 52 + cellphone;
          //Envío de SMS https://docs-latam.wavy.global/documentacion-tecnica/api-integraciones/sms-api
          $.ajax({
            type: "GET",
            statusCode: {
              403: function(xhr) {
                alertify.error("Ha ocurrido un error en el envío");
              }
            },
            url: "https://api-messaging.wavy.global/v1/send-sms",
            headers: {
              'Access-Control-Allow-Origin': '*'
            },
            dataType: "jsonp",
            contentType: "application/json",
            data: {
              username: '<?php echo $configuration["notifications_sms_username"]->value ?>',
              authenticationToken: '<?php echo $configuration["notifications_sms_authentication_token"]->value ?>',
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
                  complete: function(data) {
                    location.reload();
                  }
                });
              }
            }
          });
        }
      }
    }
  }

  function sendWhatsappNotification(reservationId = "", receptor = "", dateAtFormat = "", medicName = "", patientName = "") {
    var message = "Buen dia, te recordamos que tienes una cita agendada el dia " + dateAtFormat + " ";
    message += "<?php echo $configuration["name"]->value ?>" + " Tel: " + "<?php echo $configuration["phone"]->value ?>";
    message = message.replace(/\//g, "-");
    messageContent = removeAccents(message);

    let cellphone = receptor.replace(/ /g, "");
    console.log(cellphone);
    $.ajax({
      type: "POST",
      url: "./?action=notifications/add-whatsapp",
      data: {
        reservationId: reservationId,
        receptor: cellphone,
        message: messageContent
      },
      success: function(data) {
        window.open("https://api.whatsapp.com/send/?phone=52" + cellphone + "&text=" + messageContent + "&type=phone_number&app_absent=0");
      },
      error: function() {},
      complete: function(data) {
        location.reload();
      }
    });
  }
</script>