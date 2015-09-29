<?
require_once("../includes/db.inc.php");
require_once("../includes/functions.inc.php");
require_once("../includes/printers.inc.php");

require_once("../includes/header.inc.php");

print_admin_header("Liste des rapports");

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "create_report") {
	if(createReport($_POST['service_id'], $_POST['details'], $_POST['user_id'], -1, $_POST['name'], $_POST['solution'])==1) {
		echo popup("Création réussie");
	} else {
		echo popup("Échec de création","red");
	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "quick_grade_report") {
	if(updateReport($_POST['report_id'], $_POST['service_id'], $_POST['quick_grade'])==1) {
		echo popup("Notation réussie");
	} else {
		echo popup("Échec de notation","red");
	}
}

?>

<h2>Liste des rapports à évaluer</h2>


<?
$table = new Table();
$table->setHeader(array("UID", "Participant", "Service", "Nom", "Détails", "Correction", "Soumis à", "Quick Grade", "Final Grade", "Previous"));
$reponse = listUngradedReports();
foreach ($reponse as $value) {
	$reponse2 = listServices();
	$services = array();
	foreach ($reponse2 as $value2) {
		$services[$value2[service_id]] = $value2[service_name];
	}

	$formular = new Formular("list_reports.php", "POST");
	$formular->addHidden("action", "quick_grade_report");
	$formular->addHidden("report_id", $value[report_id]);
	//$formular->addHidden("service_id", $value[service_id]);
	$formular->addDropdown("service_id","Nom de la vuln",$services,$value[service_id]);
	$formular->addValueButtonNoDiv("button-xxsmall button-maroon", "Rejeter", "quick_grade", 0);
	$formular->addValueButtonNoDiv("button-xxsmall button-orange", "Partiel", "quick_grade", 1);
	$formular->addValueButtonNoDiv("button-xxsmall button-green", "Accepter", "quick_grade", 2);
	$formular->addValueButtonNoDiv("button-xxsmall button-blue", "Later", "quick_grade", -1);


	$table->addRow(array($value[report_id],
				mailto($value[email], "Rapport ".$value[report_id], $value[user_name]),
				$value[service_name],
				$value[name],
				nl2br($value[details]),
				nl2br($value[solution]),
				date("H:i:s", strtotime($value[time_submitted])),
				$formular->toStringNoFieldset(),
				$value[final_grade],
				internalref("report-".$value[previous_id], "#".$value[previous_id])),
			"report-".$value[report_id]);
}

print $table;
?>

<h2>Liste des rapports déjà évalués</h2>
<?
$table = new Table();
$table->setHeader(array("UID", "Participant", "Service", "Nom", "Détails", "Correction", "Soumis à", "Noté à", "Quick Grade", "Final Grade", "Previous"));
$reponse = listGradedReports();
foreach ($reponse as $value) {
	$color = getColorCode($value[quick_grade]);
	$table->addRow(array($value[report_id],
				mailto($value[email], "Rapport ".$value[report_id], $value[user_name]),
				$value[service_name],
				$value[name],
				nl2br($value[details]),
				nl2br($value[solution]),
				date("H:i:s", strtotime($value[time_submitted])),
				date("H:i:s", strtotime($value[time_graded])),
				$value[quick_grade],
				$value[final_grade],
				internalref("report-".$value[previous_id], "#".$value[previous_id])),
			"report-".$value[report_id],
			$color);
}

print $table;
?>

<p>
-1 : pas encore évalué<br/>
0 : non résolu<br/>
1 : résolution partielle<br/>
2 : résolu
</p>

<h2>Ajouter un rapport</h2>

<?
$reponse = listUsers();
$users = array();
foreach ($reponse as $value) {
	$users[$value[user_id]] = $value[user_name];
}

$reponse = listServices();
$services = array();
foreach ($reponse as $value) {
	$services[$value[service_id]] = $value[service_name];
}

$formular = new Formular("list_reports.php", "POST");
$formular->addHidden("action", "create_report");
$formular->addDropdown("user_id","Nom de l'équipe",$users);
$formular->addDropdown("service_id","Nom de la vuln",$services);
$formular->addText("name", "Nom de la vulnérabilité", "Nom", NULL);
$formular->addTextarea("details", "Descriptif de la vulnérabilité", "Détails", NULL);
$formular->addTextarea("solution", "Descriptif de la correction", "Correction", NULL);
$formular->addSubmitButton();
print $formular;


print_footer();
?>
