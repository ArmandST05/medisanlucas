<?php
//ESTUDIO DE LABORATORIO PRUEBA COVID-19
$laboratoryStudy = PatientLaboratoryStudyData::getById($_GET["id"]);

if (!isset($laboratoryStudy)) {
    echo "<script> 
          alert('El estudio no existe');
        </script>";
}

$laboratoryStudyId = $_GET["id"];
$configuration = ConfigurationData::getAll();
$patient = $laboratoryStudy->getPatient();
$months = ["01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];
$studySectionsData = PatientLaboratoryStudyData::getArraySectionOptionsByPatientStudyId($laboratoryStudyId);

$examCovidOptionsReasons = PatientLaboratoryStudyData::getAllOptionsByStudyIdSection($laboratoryStudyId, 1);
$examCovidOptionsSymptoms = PatientLaboratoryStudyData::getAllOptionsByStudyIdSection($laboratoryStudyId, 2);
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
    <input type="hidden" id="laboratoryStudyId" value="<?php echo $laboratoryStudyId ?>">
    <input type="hidden" id="patientId" value="<?php echo $patient->id ?>">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Datos del Paciente</h3>
                <div class="pull-right">
                    <a href='./?view=laboratory-studies/study-report-1&id=<?php echo $laboratoryStudyId ?>' target="_blank" class='btn btn-primary btn-xs'><i class="fas fa-file-alt"></i> Exportar PDF</a>
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
                <h3 class="box-title">PRUEBA RÁPIDA PARA LA DETECCIÓN DE ANTÍGENO COVID-19</h3>
            </div>
            <div class="box-body">
                <div class="col-lg-12">
                    <form class="form-horizontal" method="post" enctype="multipart/form-data" action="index.php?action=laboratory-studies/update" role="form">
                        <div class="row">
                            <label>Motivos para realización de pruebas</label>
                            <div class="col-lg-12">
                                <?php foreach ($examCovidOptionsReasons as $reason) : ?>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="sections[1][<?php echo $reason->id ?>]" <?php echo (trim($reason->value) != null) ? "checked" : "" ?> value="1">
                                        <label class="form-check-label"><?php echo $reason->name.$reason->value.(isset($reason->value)) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <label>Síntomas que ha presentado</label>
                            <div class="col-lg-12">
                                <?php foreach ($examCovidOptionsSymptoms as $symptom) : ?>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="sections[2][<?php echo $symptom->id ?>]" <?php echo (trim($symptom->value) != null) ? "checked" : "" ?> value="1">
                                        <label class="form-check-label"><?php echo $symptom->name.$symptom->value ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-3">Fecha y hora de toma de muestra</div>
                            <div class="col-lg-6">
                                <input class="form-control" type="datetime-local" name="date" max="<?php echo date("Y-m-d H:i:s") ?>" value="<?php echo $laboratoryStudy->date ?>" required></input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">Marca de la prueba</div>
                            <div class="col-lg-6">
                                <input class="form-control" type="text" name="sections[3]" value="<?php echo $studySectionsData[3]["value"] ?>" required></input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">REF</div>
                            <div class="col-lg-6">
                                <input class="form-control" type="text" name="sections[4]" value="<?php echo $studySectionsData[4]["value"] ?>" required></input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">LOT</div>
                            <div class="col-lg-6">
                                <input class="form-control" type="text" name="sections[5]" value="<?php echo $studySectionsData[5]["value"] ?>" required></input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">Prueba (Tipo de muestra)</div>
                            <div class="col-lg-6">
                                <input class="form-control" type="text" name="sections[6]" value="<?php echo $studySectionsData[6]["value"] ?>" required></input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">Resultado SARS-CoV-2 (COVID-19)</div>
                            <div class="col-lg-6">
                                <input class="form-control" type="text" name="sections[7]" value="<?php echo $studySectionsData[7]["value"] ?>" required></input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">Valor de Referencia (Negativo/Positivo)</div>
                            <div class="col-lg-6">
                                <input class="form-control" type="text" name="sections[8]" value="<?php echo $studySectionsData[8]["value"] ?>" required></input>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-offset-10 col-lg-2">
                                <input type="hidden" name="patientLaboratoryStudyId" value="<?php echo $laboratoryStudy->id ?>"></input>
                                <button class="btn btn-primary">Guardar datos</button>
                            </div>
                        </div>
                    </form>
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

    $(document).ready(function() {

    });
</script>