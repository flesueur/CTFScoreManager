<?php
require_once("includes/db.inc.php");
require_once("includes/functions.inc.php");
require_once("includes/printers.inc.php");

require_once("includes/header.inc.php");

print_header("CTF");

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "create_report") {
	if(createReport($_POST['service_id'], $_POST['details'], NULL, $_POST['previous_id'], $_POST['name'], $_POST['solution'])==1) {
		echo popup("Création réussie");
	} else {
		echo popup("Échec de création","red");
	}
}
?>

<div class="splash-container">
    <div class="splash">
<?php
$formular = new Formular("index.php", "POST");
$formular->addHidden("action", "create_report");
if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "update_report") {
	print "<h2>Modification du rapport ${_POST['previous_id']}</h2>";
	$formular->addText("name", "Nom de la vulnérabilité", "Nom", $_POST['name']);
	$formular->addTextarea("details", "Descriptif de la vulnérabilité", "Détails", $_POST['details']);
	$formular->addTextarea("solution", "Descriptif de la correction", "Correction", $_POST['solution']);
	$formular->addHidden("previous_id", $_POST['previous_id']);
	$formular->addHidden("service_id", $_POST['service_id']);
} else {
	print "<h2>Création d'un rapport</h2>";
	$formular->addText("name", "Nom de la vulnérabilité", "Nom", NULL);
	$formular->addTextarea("details", "Descriptif de la vulnérabilité", "Détails", NULL);
	$formular->addTextarea("solution", "Descriptif de la correction", "Correction", NULL);
	$formular->addHidden("previous_id", -1);
	$formular->addHidden("service_id", -1);
}
$formular->addSubmitButton();
print $formular;
?>

    </div>
</div>

<h2>Liste des rapports</h2>
<?php
$table = new Table();
$table->setHeader(array("UID", "Service", "Nom", "Détails", "Correction", "Quick Grade", "Final Grade", "Soumis à", "Liée à", "Modifier"));
$reponse = listMyReports();
foreach ($reponse as $value) {
	$color = getColorCode($value[quick_grade]);
	$form = new Formular("index.php", "POST");
	$form->addHidden("previous_id", $value[report_id]);
	$form->addHidden("service_id", $value[service_id]);
	$form->addHidden("details", $value[details]);
	$form->addHidden("name", $value[name]);
	$form->addHidden("solution", $value[solution]);
	$form->addHidden("action", "update_report");
	$form->addSubmitButtonNoDiv("button-xsmall", "Modifier");

	$table->addRow(array($value[report_id],
				$value[service_id],
				$value[name],
				nl2br($value[details]),
				nl2br($value[solution]),
				$value[quick_grade],
				$value[final_grade],
				date("H:i:s", strtotime($value[time_submitted])),
				$value[previous_id],
				$form->toStringNoFieldset()),
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




<?php
print_footer();
?>
