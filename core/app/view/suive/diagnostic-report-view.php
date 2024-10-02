<?php
$ageRanges = SuiveFormatData::getAllAgeRanges();
$epidemiologicalCodes = DiagnosticData::getAllEpidemiologicalCodes();

$diagnosticId = (isset($_GET["diagnosticId"]) && $_GET["diagnosticId"] != 0)  ? $_GET['diagnosticId'] : 0;
$epidemiologicalCodeId = (isset($_GET["epidemiologicalCodeId"]) && $_GET["epidemiologicalCodeId"] != 0)  ? $_GET['epidemiologicalCodeId'] : 0;

$diagnosticsReport = [];
if ($epidemiologicalCodeId) {
	$diagnosticsReport = DiagnosticData::getByEpidemiologicalCode($epidemiologicalCodeId);
}
else if ($diagnosticId) {
	$diagnosticsReport[] = DiagnosticData::getById($diagnosticId);
}
?>
<script src="assets/jquery.btechco.excelexport.js"></script>
<script src="assets/jquery.base64.js"></script>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<h1>Reporte SUIVE Diagnósticos</h1>
			<form>
				<input type="hidden" name="view" value="suive/diagnostic-report">
				<div class="row">
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label">Desde</label>
							<input type="date" name="sd" value="<?php echo (isset($_GET["sd"])) ? $_GET['sd'] : '' ?>" class="form-control" required>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label">Hasta</label>
							<input type="date" name="ed" value="<?php echo (isset($_GET["ed"])) ? $_GET['ed'] : '' ?>" class="form-control" required>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label">Diagnóstico</label>
							<select id="selectDiagnostic" class="form-control" id="combobox" onchange="changeDiagnostic(this.value)">
							</select>
							<input type="hidden" id="diagnosticId" name="diagnosticId" value="<?php echo $diagnosticId ?>">
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label">Código Epidemiológico (EPI Clave)</label>
							<select name="epidemiologicalCodeId" id="epidemiologicalCodeId" class="form-control" id="combobox" onchange="changeEpidemiologicalCode(this.value)">
							<option value="0" selected>-- SELECCIONE -- </option>
                                <?php foreach ($epidemiologicalCodes as $code) : ?>
                                    <option value="<?php echo $code->code; ?>"><?php echo $code->code ?></option>
                                <?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2 pull-right">
						<br>
						<input type="submit" class="col-md-2 btn btn-success btn-block" value="Procesar">
					</div>
					<div class="col-md-2">
						<br>
						<input type="submit" class="col-md-2 btn btn-primary btn-block" value="Exportar" id="btnExport">
					</div>
				</div>
			</form>
		</div>
	</div>
	<br>
	<!--- -->
	<div class="row">

		<div class="col-md-12">
			<?php
			$startDate = isset($_GET["sd"])  ? $_GET['sd'] : null;
			$endDate = isset($_GET["ed"])  ? $_GET['ed'] : null;
			$totalEpi = 0;
			if ($diagnosticsReport) :
			?>
				<div class="clearfix"></div>
				<h3>Casos registrados</h3>

				<table class="table table-bordered table-hover" id='datosexcel' border='1'>
					<thead>
						<tr>
							<th rowspan="3"></th>
							<th rowspan="3">Diagnóstico y Código CIE10a Revisión</th>
							<th rowspan="3">EPI Clave</th>
							<th colspan="24">Número de casos según grupo de edad y sexo </th>
							<th rowspan="2" colspan="2">Total</th>
							<th rowspan="3">TOTAL</th>
						</tr>
						<tr>
							<th colspan="2">&lt; de 1 año</th>
							<th colspan="2">1 - 4</th>
							<th colspan="2">5 - 9</th>
							<th colspan="2">10 - 14</th>
							<th colspan="2">15 - 19</th>
							<th colspan="2">20 - 24</th>
							<th colspan="2">25 - 44</th>
							<th colspan="2">45 - 49</th>
							<th colspan="2">50 - 59</th>
							<th colspan="2">60 - 64</th>
							<th colspan="2">65 Y &gt;</th>
							<th colspan="2">Ign.</th>
						</tr>
						<tr>
							<?php foreach ($ageRanges as $ageRange) : ?>
								<th>M</th>
								<th>F</th>
							<?php endforeach; ?>
							<th>M</th>
							<th>F</th>
							<th>M</th>
							<th>F</th>
						</tr>

					</thead>
					<?php
					foreach ($diagnosticsReport as $diagnostic) {
						$total = 0;
						$totalMale = 0;
						$totalFemale = 0;

						echo "<tr>
						<td></td>
						<td>" . $diagnostic->name . "</td>
						<td>" . $diagnostic->EPI_CLAVE . "</td>";
						foreach ($ageRanges as $ageRange) {
							$male = DiagnosticData::getTotalByDiagnosticIdSexRange($diagnostic->id, 1, $startDate, $endDate, $ageRange->start, $ageRange->end)->total;
							$totalMale += $male;
							echo "<td>" . $male . "</td>";
							$female = DiagnosticData::getTotalByDiagnosticIdSexRange($diagnostic->id, 2, $startDate, $endDate, $ageRange->start, $ageRange->end)->total;
							$totalFemale += $female;
							echo "<td>" . $female . "</td>";
						}
						$male = DiagnosticData::getTotalByDiagnosticIdSexNonAge($diagnostic->id, 1, $startDate, $endDate)->total;
						$totalMale += $male;
						echo "<td>" . $male . "</td>";
						$female = DiagnosticData::getTotalByDiagnosticIdSexNonAge($diagnostic->id, 2, $startDate, $endDate)->total;
						$totalFemale += $female;
						echo "<td>" . $female . "</td>";

						echo "
						<td>" . $totalMale . "</td>
						<td>" . $totalFemale . "</td>
						<td>" . ($totalMale + $totalFemale) . "</td>";
						$total += ($totalMale + $totalFemale);
					}
					?>
				</table>
				<h3 style="color:#2A8AC4">Total: <?php echo $total; ?></h3>
			<?php endif; ?>
		</div>
	</div>

	<br><br><br><br>
</section>

<script type="text/javascript">
	$(document).ready(function() {

		$("#btnExport").click(function(e) {

			$("#datosexcel").btechco_excelexport({
				containerid: "datosexcel",
				datatype: $datatype.Table,
				filename: 'Reporte SUIVE'
			});

		});


		$('#selectDiagnostic').select2({
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

		$('#epidemiologicalCodeId').select2({});

	});

	function changeDiagnostic(diagnosticData) {
		let diagnostic = diagnosticData.split("|");
		let diagnosticId = diagnostic[0];
		$("#diagnosticId").val(diagnosticId);
		$("#epidemiologicalCodeId").val(0);
	}

	function changeEpidemiologicalCode() {
		$("#diagnosticId").val(0);
	}
</script>