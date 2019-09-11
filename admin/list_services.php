<?php
require_once("../includes/db.inc.php");
require_once("../includes/functions.inc.php");
require_once("../includes/printers.inc.php");

require_once("../includes/header.inc.php");

print_admin_header("Liste des vulns et patchs");

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "create_vuln") {
	if(createVuln($_POST['service_name'], $_POST['service_patch'])==1) {
		echo popup("Création réussie");
	} else {
		echo popup("Échec de création","red");
	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "delete_vuln") {
	if(deleteVuln($_POST['service_id'])==1) {
		echo popup("Suppression réussie");
	} else {
		echo popup("Échec de suppression","red");
	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "do_update_vuln") {
	if(updateVuln($_POST['service_id'], $_POST['service_name'], $_POST['service_patch'])==1) {
		echo popup("Modification réussie");
	} else {
		echo popup("Échec de modification","red");
	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "update_vuln") {
$reponse = getServiceFromUid($_POST['service_id']);

print "<h2>Mise à jour d'une vuln</h2>";

$formular = new Formular("list_services.php", "POST");
$formular->addHidden("service_id", "${_POST['service_id']}");
$formular->addHidden("action", "do_update_vuln");
$formular->addText("service_name", "Nom", NULL, $reponse['service_name']);
$formular->addTextarea("service_patch", "Patch", NULL, $reponse['service_patch']);
$formular->addSubmitButton();
print $formular;

}

print "<h2>Liste des vulns</h2>";
$table = new Table();
$table->setHeader(array("UID", "Nom", "Patch", "Modifier", "Supprimer"));
$reponse = listServices();
foreach ($reponse as $value) {
	$modif = new Formular("list_services.php", "POST");
	$modif->addHidden("service_id", $value[service_id]);
	$modif->addHidden("action", "update_vuln");
	$modif->addSubmitButtonNoDiv(NULL, "Modifier");

	$delete = new Formular("list_services.php", "POST");
	$delete->addHidden("service_id", $value[service_id]);
	$delete->addHidden("action", "delete_vuln");
	$delete->addSubmitButtonNoDiv(NULL, "Supprimer");

	$table->addRow(array($value[service_id],
				$value[service_name],
				nl2br($value[service_patch]),
				$modif,
				$delete));
}

print $table;
?>



<h2>Ajouter une vuln</h2>

<?php
$formular = new Formular("list_services.php", "POST");
$formular->addHidden("action", "create_vuln");
$formular->addText("service_name", "Nom", "Nom", NULL);
$formular->addTextarea("service_patch", "Patch", "Patch", NULL);
$formular->addSubmitButton();
print $formular;

print_footer();
?>
