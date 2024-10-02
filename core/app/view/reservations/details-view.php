<?php
$configuration = ConfigurationData::getAll();
$reservation = ReservationData::getById($_GET["id"]);
$reservationId = $reservation->id;

if (!isset($reservation)) {
    echo "<script> 
          alert('La cita seleccionada no existe');
          window.location.href = './?view=home';
        </script>";
}
if (!$reservation->patient_id) {
    //Redireccionar agenda del doctor
    echo "<script> 
        window.location.href = './?view=reservations/edit-medic&id=" . $_GET["id"] . "';
    </script>";
}

$products = array_merge(ProductData::getAllByTypeId(1)); //Conceptos ingresos para venta
$selectedProducts = ReservationData::getProductsByReservation($reservation->id); //Productos seleccionados

$vitalSigns = ExplorationExamData::getAllByTypeReservation($reservationId, 1);
$vitalSignsArray = array_chunk($vitalSigns, 2);
$physicalExams = ExplorationExamData::getAllByTypeReservation($reservationId, 2);
$topographicalExams = ExplorationExamData::getAllByTypeReservation($reservationId, 3);
$reservationDiagnostics = DiagnosticData::getAllByReservationId($reservationId);
$reservationMedicines = MedicineData::getAllByReservationId($reservationId);

$patient = $reservation->getPatient();
$files = PatientData::getAllFilesByPatientReservation($patient->id, $reservationId);
$months = ["01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];
$reservationDateFormat = substr($reservation->date_at, 8, 2) . "/" . $months[substr($reservation->date_at, 5, 2)] . "/" . substr($reservation->date_at, 0, 4);

$laboratoryStudyCovid = PatientLaboratoryStudyData::validateByStudyTypeReservation($reservationId, 1);

//Obtener productos/servicios si se ha registrado una venta
$saleProducts = OperationDetailData::getAllProductsByReservation($reservationId);
?>
<style>
    .select2-container {
        z-index: 99999999999999;
    }

    .swal2-container {
        z-index: 99999999999999;
    }
</style>
<div class="row">
    <input type="hidden" id="reservationId" value="<?php echo $reservationId ?>">
    <input type="hidden" id="patientId" value="<?php echo $patient->id ?>">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Datos del Paciente</h3>
                <div class="pull-right">
                    <a href='./?view=patients/medical-record&patientId=<?php echo $reservation->patient_id ?>' target="_blank" class='btn btn-primary btn-xs'><i class="fas fa-file-alt"></i> Expediente paciente</a>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="col-md-3">
                    <img class="profile-user-img img-responsive img-circle" src='<?php echo ($patient->image) ? "storage_data/patients/" . $patient->image : "../../../assets/default_user.jpg" ?>' alt="Foto del paciente">
                </div>
                <div class="col-md-9">
                    <b>Nombre completo: </b><?php echo $patient->name ?><br>
                    <b>CURP: </b><?php echo $patient->curp ?><br>
                    <b>Dirección: </b><?php echo $patient->street ?> <?php echo $patient->number ?>
                    <?php echo $patient->colony ?><br>
                    <b>Teléfono: </b><?php echo $patient->cellphone ?> <br><b>Teléfono alternativo:
                    </b><?php echo $patient->homephone ?><br>
                    <b>Email: </b><?php echo $patient->email ?><br>
                    <b>Fecha nacimiento: </b><?php echo $patient->getBirthdayFormat() ?><br>
                    <b>Edad: </b><?php echo $patient->getAge() ?><br>
                    <b>Referido: </b><?php echo $patient->referred_by ?>
                </div>
            </div>
            <!-- /.box-body -->
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Datos de la Cita</h3>
                <div class="pull-right">
                    <!--<a target="blank" href='./?view=reservations/report-reservation&id=<?php echo $reservation->id ?>' class='btn btn-default btn-xs'><i class="fas fa-file-medical-alt"></i> Imprimir consulta</a>-->
                    <input type="button" value="Exportar consulta" class='btn btn-success btn-xs' id="printbutton">
                    <a href='./?view=sales/new-details&reservationId=<?php echo $_GET["id"] ?>&patientId=<?php echo $reservation->patient_id ?>&medicId=<?php echo $reservation->medic_id; ?>&date=<?php echo $reservation->date_at; ?>' class='btn btn-primary btn-xs'><i class="fas fa-dollar-sign"></i> Realizar Venta</a>
                    <a href='./?view=reservations/edit-patient&id=<?php echo $reservation->id ?>' class='btn btn-warning btn-xs'><i class="fas fa-pencil-alt"></i> Editar</a>
                    <button id="btnCancelReservation" class='btn btn-secondary btn-xs'><i class="fas fa-ban"></i>Cancelar</button>
                    <button id="btnDeleteReservation" class='btn btn-danger btn-xs'><i class="fas fa-trash"></i> Eliminar</button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <b>Fecha: </b><?php echo $reservationDateFormat; ?>
                    </div>
                    <div class="col-md-3">
                        <b>Hora: </b><?php echo $reservation->getStartTime() . " - " . $reservation->getEndTime()  ?>
                    </div>
                    <div class="col-md-3">
                        <b>Doctor: </b><?php echo $reservation->getMedic()->name ?>
                    </div>
                    <div class="col-md-3">
                        <b>Agendado por: </b><?php echo $reservation->getMedic()->name ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <b>Área: </b><?php echo $reservation->getArea()->name  ?>
                    </div>
                    <div class="col-md-3">
                        <b>Categoría: </b><?php echo $reservation->getCategory()->name; ?>
                    </div>
                    <div class="col-md-3">
                        <b>Laboratorio: </b><?php echo $reservation->getLaboratory()->name ?>
                    </div>
                    <div class="col-md-3">
                        <label for="inputEmail1">Estatus:</label>
                        <label id="reservation_status_name"><?php echo $reservation->getStatus()->name ?></label>
                        <input type="hidden" id="status_id" value="<?php echo $reservation->status_id ?>"></input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="inputEmail1">Motivo de la Consulta:</label>
                        <textarea class="form-control" id="reason" name="reason" placeholder="Motivo de la Consulta" required><?php echo $reservation->reason; ?></textarea>
                    </div>
                </div>
                <br>
                <?php if ($saleProducts) : ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="control-label">Servicios cobrados: </label>
                            <textarea class="form-control" disabled><?php foreach ($saleProducts as $saleProduct) {
                                                                        $productData = ProductData::getById($saleProduct->product_id);
                                                                        echo trim($productData->name) . " ($" . trim($saleProduct->price) . "),";
                                                                    } ?></textarea>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="control-label">Servicios a realizar: </label>
                            <label id="lbTotalProducts">(Total: $0.00)
                                <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="Puedes indicar los servicios o conceptos/ingresos para posteriormente cobrarlos."></i>
                            </label>
                            <select name="products[]" id="products" class="form-control">
                                <?php foreach ($products as $product) : ?>
                                    <option value="<?php echo $product->id; ?>" data-price-out="<?php echo $product->price_out; ?>"><?php echo $product->name . " | $" . number_format($product->price_out, 2); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <br>
                <?php if ($reservation->status_id != 2) : ?>
                    <div class="row">
                        <div class="col-md-2 pull-right">
                            <button id="btnStartConsultation" onclick="updateReservationStatus(2)" class='btn btn-primary btn-xs'>Comenzar Consulta <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div id="medicalConsultationDetails">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(S) Subjetivo - Observaciones del paciente</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <textarea class="form-control" id="patientObservations" name="patientObservations" placeholder="Observaciones del paciente"><?php echo $reservation->patient_observations; ?></textarea>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(O) Objetivo - Signos Vitales</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <?php foreach ($vitalSignsArray as $vitalSigns) : ?>
                        <div class="row">
                            <?php foreach ($vitalSigns as $vitalSign) : ?>
                                <div class="col-md-3">
                                    <?php echo $vitalSign->name ?>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="explorationExam<?php echo $vitalSign->id ?>" value="<?php echo $vitalSign->value ?>" placeholder="<?php echo $vitalSign->name ?>" onkeyup="updateVitalSign('<?php echo $vitalSign->id ?>')" autofocus>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="box box-details box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(O) Objetivo - Examen Físico</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-10">
                            <select name="physicalExam" id="physicalExam" class="form-control" id="combobox" autofocus required onchange="addPhysicalExam()">
                                <option value="" disabled selected>-- SELECCIONE -- </option>
                                <?php foreach ($physicalExams as $physicalExam) : ?>
                                    <option value="<?php echo $physicalExam->id; ?>"><?php echo $physicalExam->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row" id="divPhysicalExamsDetail">

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="box-title">Observaciones de examen físico</h4>
                            <textarea class="form-control" id="physicalObservations" name="physicalObservations" placeholder="Observaciones de examen físico"><?php echo $reservation->physical_observations; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(O) Objetivo - Exploración Topográfica</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-10">
                            <select name="topographicalExam" id="topographicalExam" class="form-control" id="combobox" autofocus required onchange="addTopographicalExam()">
                                <option value="" disabled selected>-- SELECCIONE -- </option>
                                <?php foreach ($topographicalExams as $topographicalExam) : ?>
                                    <option value="<?php echo $topographicalExam->id; ?>"><?php echo $topographicalExam->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row" id="divTopographicalExamsDetail">
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="box-title">Observaciones de exploración topográfica</h4>
                            <textarea class="form-control" id="topographicalObservations" name="topographicalObservations" placeholder="Observaciones de examen topográfico"><?php echo $reservation->topographical_observations; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(O) Objetivo - Archivos, imágenes y exámenes</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-sm btn-primary" onclick="addFile()"><i class="fas fa-upload"></i> Subir archivo</button>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row" id="divFiles">
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(O) Objetivo - Exámenes de Laboratorios</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <button id="btnLaboratoryStudy1" class="btn btn-sm btn-primary" onclick="addStudyCovid()"><i class="fas fa-plus"></i> Nuevo examen COVID-19</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <a id="linkLaboratoryStudy1" class="btn btn-sm btn-primary" target="_blank" href=""><i class="fas fa-pencil-alt"></i> Capturar examen COVID-19</a>
                            <a id="linkPdfLaboratoryStudy1" class="btn btn-sm btn-default" target="_blank" href=""><i class="fas fa-file"></i> PDF examen COVID-19</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(A) Análisis - Diagnósticos</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <select name="selectDiagnostics" id="selectDiagnostics" class="form-control" required onchange="selectDiagnostic(this.value)">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover">
                                <tbody id="diagnosticsTable">
                                    <?php foreach ($reservationDiagnostics as $diagnostic) : ?>
                                        <tr id="d<?php echo $diagnostic->reservation_detail_id ?>">
                                            <td><?php echo $diagnostic->catalog_key . " | " . $diagnostic->name ?></td>
                                            <td>
                                                <input class="form-control" type="text" id="<?php echo 'dv' . $diagnostic->reservation_detail_id ?>" value="<?php echo $diagnostic->value; ?>" onkeyup="updateDiagnostic('<?php echo $diagnostic->reservation_detail_id ?>')"></input>
                                            </td>
                                            <td><button class='btn btn-danger btn-xs' onclick="deleteDiagnostic('<?php echo $diagnostic->reservation_detail_id ?>')"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="box-title">Observaciones de diagnósticos</h4>
                            <textarea class="form-control" id="diagnosticObservations" name="diagnosticObservations" placeholder="Observaciones de diagnósticos"><?php echo $reservation->diagnostic_observations; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(A) Plan - Observaciones de tratamiento o plan</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <textarea class="form-control" id="treatmentObservations" name="treatmentObservations" placeholder="Observaciones de tratamiento o plan"><?php echo $reservation->treatment_observations; ?></textarea>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">(P) Plan - Receta de Medicamentos</h3>
                    <div class="box-tools pull-right">
                        <?php if ($configuration['active_personalized_prescription']->value == 1) : ?>
                            <a href='./?view=reservations/report-prescription-personalized&id=<?php echo $reservationId ?>' target="__blank" class='btn btn-primary btn-xs'><i class="fas fa-file-medical"></i> Receta Médica</a>
                        <?php else : ?>
                            <a href='./?view=reservations/report-prescription&id=<?php echo $reservationId ?>' target="__blank" class='btn btn-primary btn-xs'><i class="fas fa-file-medical"></i> Receta Médica</a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <select name="selectMedicines" id="selectMedicines" class="form-control" required onchange="selectMedicine(this.value)">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <th width="100px;"># Receta<i class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="Selecciona varias recetas para separar medicamentos controlados."></i></th>
                                    <th width="250px;">Medicamento</th>
                                    <th width="120px;">Tomar</th>
                                    <th width="200px;">Frecuencia</th>
                                    <th width="120px;">Duración</th>
                                    <th>Notas</th>
                                    <th></th>
                                </thead>
                                <tbody id="medicinesTable">
                                    <?php foreach ($reservationMedicines as $medicine) : ?>
                                        <tr id="m<?php echo $medicine->reservation_detail_id ?>">
                                            <td>
                                                <input class="form-control" type="number" min="1" max="5" id="<?php echo 'mprescription_number' . $medicine->reservation_detail_id ?>" value="<?php echo $medicine->prescription_number; ?>" onchange="updateMedicine('<?php echo $medicine->reservation_detail_id; ?>','prescription_number')"></input>
                                            </td>
                                            <td><label><?php echo $medicine->generic_name . "|" . $medicine->pharmaceutical_form ?></label><?php echo " <br>" . $medicine->concentration . "<br>" . $medicine->presentation ?></td>
                                            <td>
                                                <input class="form-control mquantity" name="mquantity" autocomplete="mquantity" type="text" id="<?php echo 'mquantity' . $medicine->reservation_detail_id ?>" value="<?php echo $medicine->quantity; ?>" onkeyup="updateMedicine('<?php echo $medicine->reservation_detail_id; ?>','quantity')"></input>
                                            </td>
                                            <td>
                                                <input class="form-control mfrequency" name="mfrequency" autocomplete="mfrequency" type="text" id="<?php echo 'mfrequency' . $medicine->reservation_detail_id ?>" value="<?php echo $medicine->frequency; ?>" onkeyup="updateMedicine('<?php echo $medicine->reservation_detail_id; ?>','frequency')"></input>
                                            </td>
                                            <td>
                                                <input class="form-control mduration" name="mduration" autocomplete="mduration" type="text" id="<?php echo 'mduration' . $medicine->reservation_detail_id ?>" value="<?php echo $medicine->duration; ?>" onkeyup="updateMedicine('<?php echo $medicine->reservation_detail_id; ?>','duration')"></input>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" id="<?php echo 'mdescription' . $medicine->reservation_detail_id ?>" value="<?php echo $medicine->description; ?>" onkeyup="updateMedicine('<?php echo $medicine->reservation_detail_id; ?>','description')"></input>
                                            </td>
                                            <td><button class='btn btn-danger btn-xs' onclick="deleteMedicine('<?php echo $medicine->reservation_detail_id ?>')"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="box-title">Observaciones extra de receta</h4>
                            <textarea class="form-control" name="observationsPrescription" id="observationsPrescription" placeholder="Observaciones de diagnósticos"><?php echo $reservation->observations_prescription; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<script>
    var Toast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 3000
    });

    var reasonNicEditor = new nicEditor().panelInstance('reason');

    var patientObservationsNicEditor = new nicEditor().panelInstance('patientObservations');
    var diagnosticObservationsNicEditor = new nicEditor().panelInstance('diagnosticObservations');
    var treatmentObservationsNicEditor = new nicEditor().panelInstance('treatmentObservations');
    var physicalObservationsNicEditor = new nicEditor().panelInstance('physicalObservations');
    var topographicalObservationsNicEditor = new nicEditor().panelInstance('topographicalObservations');
    var observationsPrescriptionNicEditor = new nicEditor().panelInstance('observationsPrescription');

    $(document).ready(function() {

        $("#physicalExam").select2({});
        $("#topographicalExam").select2({});

        $('#selectDiagnostics').select2({
            placeholder: "Escribe el nombre o clave del diagnóstico",
            minimumInputLength: 3,
            ajax: {
                url: "./?action=diagnostics/get-search", // json datasource
                type: 'GET',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data
                    }
                }
            }
        });

        $('#selectMedicines').select2({
            language: "es",
            placeholder: "Escribe el nombre del medicamento",
            minimumInputLength: 3,
            ajax: {
                url: "./?action=medicines/get-search", // json datasource
                type: 'GET',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data
                    }
                }
            }
        });

        validateConsultationDetails();
        showExplorationExams(2);
        showExplorationExams(3);

        //Inicializar editores personalizados
        document.getElementById('reason').parentElement.onkeypress = function() {
            updateReason();
        }
        document.getElementById('patientObservations').parentElement.onkeydown = function() {
            updatePatientObservations();
        }
        document.getElementById('diagnosticObservations').parentElement.onkeydown = function() {
            updateDiagnosticObservations();
        }
        document.getElementById('treatmentObservations').parentElement.onkeydown = function() {
            updateTreatmentObservations();
        }
        document.getElementById('physicalObservations').parentElement.onkeydown = function() {
            updatePhysicalObservations();
        }
        document.getElementById('topographicalObservations').parentElement.onkeydown = function() {
            updateTopographicalObservations();
        }
        document.getElementById('observationsPrescription').parentElement.onkeypress = function() {
            updateObservationsPrescription();
        }

        showFiles(); //Mostrar archivos subidos

        $(function() {
            var availableQuantity = [
                "1 pastilla",
                "2 pastillas",
                "1 cucharada",
                "2 cucharadas",
                "1 supositorio",
            ];
            var availableFrequency = [
                "1 hora",
                "4 horas",
                "8 horas",
                "12 horas",
                "24 horas",
            ];
            var availableDuration = [
                "1 día",
                "2 días",
                "7 días",
                "14 días",
            ];
            $(".mquantity").autocomplete({
                source: availableQuantity
            });
            $(".mfrequency").autocomplete({
                source: availableFrequency
            });
            $(".mduration").autocomplete({
                source: availableDuration
            });
        });

        //Cargar datos de estudio de laboratorio
        if ("<?php echo isset($laboratoryStudyCovid) ?>" == true) {
            $("#btnLaboratoryStudy1").hide();
            $("#linkLaboratoryStudy1").show();
            $("#linkLaboratoryStudy1").attr("href", "index.php?view=laboratory-studies/study-1&id=" + "<?php echo ($laboratoryStudyCovid) ? $laboratoryStudyCovid->id : '' ?>");
            $("#linkPdfLaboratoryStudy1").show();
            $("#linkPdfLaboratoryStudy1").attr("href", "index.php?view=laboratory-studies/study-report-1&id=" + "<?php echo ($laboratoryStudyCovid) ? $laboratoryStudyCovid->id : '' ?>");
        } else {
            $("#btnLaboratoryStudy1").show();
            $("#linkPdfLaboratoryStudy1").hide();
            $("#linkLaboratoryStudy1").hide();
            $("#linkLaboratoryStudy1").attr("href", "");
        }

        $("#products").select2({
            multiple: true,
            allowClear: true
        });
        $("#products").val(null).trigger("change");

        //Cargar datos por defecto en el select productos
        let selectedProducts = JSON.parse('<?php echo json_encode($selectedProducts) ?>');
        let selectedProductsArray = [];

        $(selectedProducts).each(function(selectedProduct) {
            selectedProductsArray.push(this.product_id);
        });

        $("#products").val(selectedProductsArray).trigger("change");
        calculateTotalProducts();

        $('#products').on('select2:select', function(e) {
            $.ajax({
                url: "./?action=reservation-products/add", // json datasource
                type: "POST", // method, by default get
                data: {
                    "reservationId": "<?php echo $reservationId; ?>",
                    "productId": e.params.data.id
                },
                success: function() {
                    calculateTotalProducts();
                },
                error: function() { // error handling
                    Swal.fire(
                        'Error',
                        'El concepto/servicio no se ha podido agregar. Recarga la página.',
                        'error'
                    );
                }
            });
        });
        $('#products').on('select2:unselect', function(e) {
            $.ajax({
                url: "./?action=reservation-products/delete", // json datasource
                type: "POST", // method, by default get
                data: {
                    "reservationId": "<?php echo $reservationId; ?>",
                    "productId": e.params.data.id
                },
                success: function() {
                    calculateTotalProducts();
                },
                error: function() { // error handling
                    Swal.fire(
                        'Error',
                        'El concepto/servicio no se ha podido eliminar. Recarga la página.',
                        'error'
                    );
                }
            });
        });
        $('#products').on('select2:clear', function(e) {
            $.ajax({
                url: "./?action=reservation-products/delete-all", // json datasource
                type: "POST", // method, by default get
                data: {
                    "reservationId": "<?php echo $reservationId; ?>"
                },
                success: function() {
                    calculateTotalProducts();
                },
                error: function() { // error handling
                    Swal.fire(
                        'Error',
                        'Los conceptos/servicios no se han podido eliminar. Recarga la página.',
                        'error'
                    );
                }
            });
        });
    });

    function validateConsultationDetails() {
        //Muestra y oculta los detalles de la consulta
        //Añade el color al estatus de la cita
        $("#reservation_status_name").removeClass(); //Borra todas las clases

        if ($("#status_id").val() == "2") {
            //Asistió paciente
            $("#medicalConsultationDetails").show();
            $("#reservation_status_name").addClass("btn-primary");
        } else if ($("#status_id").val() == "3") {
            //Cancelado
            $("#reservation_status_name").addClass("btn-danger");
            $("#medicalConsultationDetails").hide();
        } else {
            $("#medicalConsultationDetails").hide();
        }
    }

    /*-----------------RESERVATION OPTIONS-------------*/
    $("#btnDeleteReservation").click(function() {
        Swal.fire({
            title: '¿Estás seguro de eliminar la cita?',
            text: "¡No serás capaz de revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, Eliminar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "./?action=reservations/delete-reservation", // json datasource
                    type: "POST", // method, by default get
                    data: "id=" + "<?php echo $reservationId; ?>",
                    success: function() {
                        window.location = 'index.php?view=home';
                    },
                    error: function() { // error handling
                        Swal.fire(
                            'Error',
                            'La cita no se ha podido eliminar.',
                            'error'
                        );
                    }
                });
            }
        })
    });

    $("#btnCancelReservation").click(function() {
        Swal.fire({
            title: '¿Estás seguro de cancelar la cita?',
            text: "Al cancelar la cita indicas que el paciente no asistó.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, Cancelar'
        }).then((result) => {
            if (result.value) {
                updateReservationStatus(3);
            }
        })
    });

    /*---------AÑADIR ARCHIVOS----------*/
    function addFile() {
        Swal.fire({
            title: 'Archivo',
            input: 'file',
            inputAttributes: {
                'accept': '/*',
                'aria-label': 'Selecciona el archivo',
            },
            onBeforeOpen: () => {
                $(".swal2-file").change(function() {
                    var reader = new FileReader();
                    reader.readAsDataURL(this.files[0]);
                });
            },

            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            inputValidator: (value) => {
                if (!value) {
                    return '¡Selecciona un archivo!'
                }
            },
            preConfirm: (value) => {
                var formData = new FormData();
                formData.append('patientId', $("#patientId").val());
                formData.append('reservationId', $("#reservationId").val());

                var file = $('.swal2-file')[0].files[0];
                formData.append("files", file);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "./?action=patient-files/add",
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData,
                    error: function() {
                        Swal.fire(
                            '¡Oops!',
                            'El archivo no se ha podido guardar.',
                            'error'
                        )
                    },
                    success: function(data) {
                        showFiles();
                    }
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    }


    function deleteFile(id) {
        $.ajax({
            url: "./?action=patient-files/delete-reservation",
            type: "POST",
            data: {
                id: id
            },
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: 'Archivo eliminado.'
                });
                showFiles();
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al eliminar el archivo.'
                });
            }
        });
    }

    function showFiles() {
        $.ajax({
            url: "./?action=patient-files/get-reservation",
            type: "GET",
            data: {
                reservationId: $("#reservationId").val(),
                patientId: $("#patientId").val(),
            },
            success: function(data) {
                $("#divFiles div").remove()
                $("#divFiles").append(data);
            }
        });
    }

    /*---------------LABORATORY STUDIES----------------- */
    function addStudyCovid() {
        Swal.fire({
            title: '¿Estás seguro de crear un nuevo estudio COVID-19?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, crearlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "./?action=laboratory-studies/add",
                    type: "POST",
                    data: {
                        reservationId: $("#reservationId").val(),
                        patientId: $("#patientId").val(),
                        laboratoryStudyId: 1,
                        date: "<?php date("Y-m-d H:i:s") ?>",
                    },
                    success: function(data) {
                        $("#btnLaboratoryStudy1").hide();
                        $("#linkPdfLaboratoryStudy1").show();
                        $("#linkLaboratoryStudy1").show();
                        $("#linkLaboratoryStudy1").attr("href", "index.php?view=laboratory-studies/study-1&id=" + data);
                        $("#linkPdfLaboratoryStudy1").attr("href", "index.php?view=laboratory-studies/study-report-1&id=" + data);
                    },
                    error: function() {
                        $("#btnLaboratoryStudy1").show();
                        $("#linkPdfLaboratoryStudy1").hide();
                        $("#linkLaboratoryStudy1").hide();
                    }
                });
            }
        })

    }
    /*---------------RESERVATION REASON----------------- */

    function updateReason() {
        let reason = nicEditors.findEditor('reason').getContent();
        $.ajax({
            url: "./?action=reservations/update-reason-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                reason: reason
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Actualizado Motivo de la Cita.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar el motivo de la cita.'
                });
            }
        });
    }

    
    function updateObservationsPrescription() {
        let observationsPrescription = nicEditors.findEditor('observationsPrescription').getContent();
        $.ajax({
            url: "./?action=reservations/update-observations-prescription", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                observations: observationsPrescription
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Actualizadas observaciones de la receta.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar observaciones de la receta.'
                });
            }
        });
    }

    /*---------------RESERVATION PATIENT OBSERVATIONS----------------- */

    function updatePatientObservations() {
        let patientObservations = nicEditors.findEditor('patientObservations').getContent();
        $.ajax({
            url: "./?action=reservations/update-patient-observations", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                patientObservations: patientObservations
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Actualizadas observaciones del paciente.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar las observaciones del paciente.'
                });
            }
        });
    }

    /*---------------RESERVATION DIAGNOSTIC OBSERVATIONS----------------- */

    function updateDiagnosticObservations() {
        let diagnosticObservations = nicEditors.findEditor('diagnosticObservations').getContent();
        $.ajax({
            url: "./?action=reservations/update-diagnostic-observations", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                diagnosticObservations: diagnosticObservations
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Actualizadas observaciones de diagnósticos.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar las observaciones de diagnósticos.'
                });
            }
        });
    }

    /*---------------RESERVATION TREATMENT OBSERVATIONS----------------- */

    function updateTreatmentObservations() {
        let treatmentObservations = nicEditors.findEditor('treatmentObservations').getContent();
        $.ajax({
            url: "./?action=reservations/update-treatment-observations", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                treatmentObservations: treatmentObservations
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Actualizadas observaciones de tratamiento/plan.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar las observaciones de tratamiento/plan.'
                });
            }
        });
    }

    /*---------------RESERVATION PHYSICAL OBSERVATIONS----------------- */

    function updatePhysicalObservations() {
        let physicalObservations = nicEditors.findEditor('physicalObservations').getContent();
        $.ajax({
            url: "./?action=reservations/update-physical-observations", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                physicalObservations: physicalObservations
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Actualizadas observaciones de examen físico.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar las observaciones de examen físico.'
                });
            }
        });
    }

    /*---------------RESERVATION TOPOGRAPHICAL OBSERVATIONS----------------- */

    function updateTopographicalObservations() {
        let topographicalObservations = nicEditors.findEditor('topographicalObservations').getContent();
        $.ajax({
            url: "./?action=reservations/update-topographical-observations", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                topographicalObservations: topographicalObservations
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Actualizadas observaciones de examen topográfico.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar las observaciones de examen topográfico.'
                });
            }
        });
    }


    /*--------------VITAL SIGNS---------------- */
    function updateVitalSign(id) {
        //Se creó una función sólo para los signos vitales
        let value = $('#explorationExam' + id).val();

        $.ajax({
            url: "./?action=exploration-exams/update-vital-sign-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                explorationExamId: id,
                reservationId: $("#reservationId").val(),
                value: value
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Información Actualizada.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar la información.'
                });
            }
        });
    }

    /*--------------EXPLORATION EXAMS----------*/
    function showExplorationExams(explorationExamTypeId) {
        $.ajax({
            url: "./?action=exploration-exams/get-reservation",
            type: "GET",
            data: {
                reservationId: $("#reservationId").val(),
                explorationExamTypeId: explorationExamTypeId,
            },
            success: function(data) {
                if (explorationExamTypeId == 2) {
                    $("#divPhysicalExamsDetail div").remove()
                    $("#divPhysicalExamsDetail").append(data);
                } else if (explorationExamTypeId == 3) {
                    $("#divTopographicalExamsDetail div").remove()
                    $("#divTopographicalExamsDetail").append(data);
                }
            }
        });
    }

    function updateExplorationExam(id) {
        //let value = nicEditors.findEditor('explorationExam' + id).getContent();
        let value = $('#explorationExam' + id).val();

        $.ajax({
            url: "./?action=exploration-exams/update-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                id: id,
                value: value
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Información Actualizada.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar la información.'
                });
            }
        });
    }

    function deleteExplorationExam(id, explorationExamTypeId) {
        $.ajax({
            url: "./?action=exploration-exams/delete-reservation",
            type: "POST",
            data: {
                id: id
            },
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: 'Información eliminada.'
                });
                showExplorationExams(explorationExamTypeId);
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al eliminar la información.'
                });
            }
        });
    }

    function addPhysicalExam() {
        $.ajax({
            url: "./?action=exploration-exams/add-reservation",
            type: "POST",
            data: {
                reservationId: $("#reservationId").val(),
                explorationExamId: $("#physicalExam").val(),
            },
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: 'Examen físico agregado.'
                });
                showExplorationExams(2);
            }
        });
    }

    function addTopographicalExam() {
        $.ajax({
            url: "./?action=exploration-exams/add-reservation",
            type: "POST",
            data: {
                reservationId: $("#reservationId").val(),
                explorationExamId: $("#topographicalExam").val(),
            },
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: 'Examen topográfico agregado.'
                });
                showExplorationExams(3);
            }
        });
    }
    /*--------------EXPLORATION EXAMS----------*/

    function updateReservationStatus(status) {
        $.ajax({
            url: "./?action=reservations/update-status-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                statusId: status
            },
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: 'Actualizado Estatus de la Cita.'
                });
                let datos = JSON.parse(data);

                $("#reservation_status_name").text(datos[
                    'name']); //Actualizar nombre del estatus de la reservación
                $("#status_id").val(datos['id']); //Actualizar estatus id de la reservación
                validateConsultationDetails();
                if (status == 2) {
                    $("#btnStartConsultation").hide();
                }
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar el Estatus de la Cita.'
                });
            }
        });
    }

    /*--------------DIAGNOSTICS----------*/
    function selectDiagnostic(diagnosticData) {
        let diagnostic = diagnosticData.split("|");
        let diagnosticId = diagnostic[0];
        let diagnosticCatalogKey = diagnostic[1];
        let diagnosticName = diagnostic[2];

        $.ajax({
            url: "./?action=reservations/add-diagnostic-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                diagnosticId: diagnosticId,
            },
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: 'Diagnóstico agregado.'
                });
                let trTable = "<tr id='d" + data + "'><td>" + diagnosticCatalogKey + " | " + diagnosticName + "</td>";
                trTable += "<td><input class='form-control' type='text' id='dv" + data + "' value='' onkeyup='updateDiagnostic(" + data + ")'></input></td>";
                trTable += "<td><button class='btn btn-danger btn-xs' onclick='deleteDiagnostic(" + data + ")'><i class='fas fa-trash'></i></button></td></tr>";

                $("#diagnosticsTable").append(trTable);

            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al añadir el diagnóstico.'
                });
            }
        });
    }

    function updateDiagnostic(id) {
        let diagnosticValue = $('#dv' + id).val();
        $.ajax({
            url: "./?action=reservations/update-diagnostic-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                id: id,
                value: diagnosticValue
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Información Actualizada.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al actualizar la información.'
                });
            }
        });
    }

    function deleteDiagnostic(reservationDiagnosticId) {
        $.ajax({
            url: "./?action=reservations/delete-diagnostic-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationDiagnosticId: reservationDiagnosticId
            },
            success: function() {
                $("#d" + reservationDiagnosticId).remove();
                Toast.fire({
                    icon: 'success',
                    title: 'Diagnóstico eliminado.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al eliminar el diagnóstico.'
                });
            }
        });
    }

    /*--------------MEDICINES----------*/
    function selectMedicine(medicineData) {
        let medicine = medicineData.split("|");
        let medicineId = medicine[0];
        let medicineGenericName = medicine[1];
        let medicinePharmaceuticalForm = medicine[2];
        let medicineConcentration = medicine[3];
        let medicinePresentation = medicine[4];

        $.ajax({
            url: "./?action=reservations/add-medicine-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationId: "<?php echo $reservationId; ?>",
                medicineId: medicineId,
            },
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: 'Medicamento agregado.'
                });
                let trTable = '<tr id="m' + data + '"><td><input class="form-control" type="number" min="1" max="5" id="mprescription_number' + data + '" value="1" onchange="updateMedicine(' + data + ',`prescription_number`)"></input></td>';
                trTable += '<td><label>' + medicineGenericName + '|' + medicinePharmaceuticalForm + '</label><br>' + medicineConcentration + '<br>' + medicinePresentation + '</td>';
                trTable += '<td><input class="form-control" type="text" id="mquantity' + data + '" onkeyup="updateMedicine(' + data + ',`quantity`)"></input></td>';
                trTable += '<td><input class="form-control" type="text" id="mfrequency' + data + '" onkeyup="updateMedicine(' + data + ',`frequency`)"></input></td>';
                trTable += '<td><input class="form-control" type="text" id="mduration' + data + '" onkeyup="updateMedicine(' + data + ',`duration`)"></input></td>';
                trTable += '<td><input class="form-control" type="text" id="mdescription' + data + '" onkeyup="updateMedicine(' + data + ',`description`)"></input></td>';
                trTable += "<td><button class='btn btn-danger btn-xs' onclick='deleteMedicine(" + data + ")'><i class='fas fa-trash'></i></button></td></tr>";

                $("#medicinesTable").append(trTable);

                //Actualizar autocompletar
                $(".mquantity").autocomplete({
                    source: availableQuantity
                });
                $(".mfrequency").autocomplete({
                    source: availableFrequency
                });
                $(".mduration").autocomplete({
                    source: availableDuration
                });

            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al añadir el medicamento.'
                });
            }
        });
    }

    function updateMedicine(id, column) {
        let medicineValue = $('#m' + column + id).val();
        //Puede haber varias recetas, para medicamentos especiales, se seleciona el # de página en la que se muestra.
        if (column == "prescription_number" && (medicineValue == "" || medicineValue == 0 || medicineValue > 5)) {
            medicineValue = 1;
            $('#m' + column + id).val(1);
        }

        $.ajax({
            url: "./?action=reservations/update-medicine-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                id: id,
                column: column,
                value: medicineValue
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Información Actualizada.'
                });
            },
            error: function() { // error handling
                Swal.fire({
                    icon: 'warning',
                    title: 'No se ha podido guardar toda la información.',
                    text: 'Por favor, intenta nuevamente.',
                    footer: 'Esto ocurre normalmente por intermitencias en tu conexión a internet.',
                    confirmButtonText: 'Intentar nuevamente.',
                }).then((result) => {
                    if (result.value) {
                        updateMedicine(id, column);
                    }
                });
            }
        });
    }

    function deleteMedicine(reservationMedicineId) {
        $.ajax({
            url: "./?action=reservations/delete-medicine-reservation", // json datasource
            type: "POST", // method, by default get
            data: {
                reservationMedicineId: reservationMedicineId
            },
            success: function() {
                $("#m" + reservationMedicineId).remove();
                Toast.fire({
                    icon: 'success',
                    title: 'Medicamento eliminado.'
                });
            },
            error: function() { // error handling
                Toast.fire({
                    icon: 'error',
                    title: 'Error al eliminar el medicamento.'
                });
            }
        });
    }

    /*-----------------IMC------------- */
    $('#explorationExam' + 10).change(function() { //Peso
        calculateImc();
    });

    $('#explorationExam' + 12).change(function() { //Estatura
        calculateImc();
    });

    function calculateImc() {
        let imc = 0;
        let height = parseFloat($('#explorationExam' + 12).val());
        let weight = parseFloat($('#explorationExam' + 10).val());

        imc = weight / (height * height);
        if (isNaN(imc)) {
            imc = "";
        }
        $('#explorationExam' + 11).val(imc.toFixed(2));
        updateVitalSign(11);
    }

    $("#printbutton").click(function() {
        print();
    });

    /*-------------PRODUCTS/SERVICES--------------- */
    function calculateTotalProducts() {
        //Calcula el total a pagar por los productos(conceptos/ingresos) seleccionados
        let totalProducts = 0;
        let selectedProducts = $('#products').select2('data');

        $(selectedProducts).each(function(index, product) {
            let value = product.element.attributes['data-price-out'].nodeValue;
            totalProducts += ((isNaN(parseFloat(value))) ? 0 : parseFloat(value));
        });

        $("#lbTotalProducts").text("Total: $" + parseFloat(totalProducts).toFixed(2));
    }
</script>