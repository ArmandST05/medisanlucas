<?php
$explorationExamTypeId = $_GET["explorationExamTypeId"];
$reservationId = $_GET["reservationId"];

$explorationExams = ExplorationExamData::getByTypeReservation($reservationId, $explorationExamTypeId);

foreach ($explorationExams as $explorationExam) {
    echo '<div class="col-md-6">
        <div class="box box-secondary">
            <div class="box-header with-border">
                <label>'.$explorationExam->name.'</label>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="remove" onclick="deleteExplorationExam('.$explorationExam->id.','.$explorationExamTypeId.')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <textarea type="text" class="form-control" id="explorationExam' . $explorationExam->id.'" placeholder="'.$explorationExam->name.'" onkeyup="updateExplorationExam('.$explorationExam->id.')">'.$explorationExam->value.'</textarea>
            </div>
        </div>
    </div>';
    }
?>
