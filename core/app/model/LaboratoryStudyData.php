<?php
class LaboratoryStudyData {
	public static $tablename = "laboratory_studies";
	public static $tablenameSections = "laboratory_study_sections";
	public static $tablenameSectionOptions = "laboratory_study_section_options";
	public $id;
	public $reservation_id;
	public $exploration_exam_type_id;
	public $date;
	public $created_at;

	public function __construct(){
		$this->created_at = "NOW()";
	}

	
	


}
?>