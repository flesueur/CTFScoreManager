<?
require_once("../includes/db.inc.php");
require_once("../includes/functions.inc.php");
require_once("../includes/printers.inc.php");

require_once("../includes/header.inc.php");

print_admin_header("Liste des utilisateurs");

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "delete_account") {
	if(deleteAccount($_POST['user_id'])==1) {
		echo popup("Suppression réussie");
	} else {
		echo popup("Échec de suppression","red");
	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "do_update_account") {
	if(updateAccount($_POST['user_id'], $_POST['user_name'], $_POST['email'], $_POST['password'], $_POST['isAdmin'], $_POST['isLocal'])==1) {
		echo popup("Modification réussie");
	} else {
		echo popup("Échec de modification","red");
	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "update_account") {
$reponse = getAccountFromUid($_POST['user_id']);

echo <<< EOT
<h2>Mise à jour d'une équipe</h2>
EOT;
$formular = new Formular("list_users.php", "POST");
$formular->addHidden("user_id", $_POST['user_id']);
$formular->addHidden("action", "do_update_account");
$formular->addText("user_name", "Nom", NULL, $reponse['user_name']);
$formular->addText("email", "Email", NULL, $reponse['email']);
$formular->addText("password", "Password", NULL, $reponse['password']);
$formular->addText("isAdmin", "isAdmin", NULL, $reponse['is_admin']);
$formular->addText("isLocal", "isLocal", NULL, $reponse['is_local']);
$formular->addSubmitButton();
print $formular;
}
?>

<h2>Liste des équipes</h2>
<?
$table = new Table();
$table->setHeader(array("UID", "Nom", "Email", "isAdmin", "isLocal", "Modifier", "Supprimer"));
$reponse = listUsers();
foreach ($reponse as $value) {
	$modif = new Formular("list_users.php", "POST");
	$modif->addHidden("user_id", $value[user_id]);
	$modif->addHidden("action", "update_account");
	$modif->addSubmitButtonNoDiv("button-xsmall", "Modifier");

	$delete = new Formular("list_users.php", "POST");
	$delete->addHidden("user_id", $value[user_id]);
	$delete->addHidden("action", "delete_account");
	$delete->addSubmitButtonNoDiv("button-xsmall", "Supprimer");

	$table->addRow(array($value[user_id],
				$value[user_name],
				mailto($value[email], "", $value[email]),
				$value[is_admin],
				$value[is_local],
				$modif->toStringNoFieldset(),
				$delete->toStringNoFieldset()));
}

print $table;
?>

<h2>Liste des participants</h2>
<?
$table = new Table();
$table->setHeader(array("UID", "Équipe", "Nom", "Prénom", "Email", "Université", "Cursus"));
$reponse = listPeople();
foreach ($reponse as $value) {
	$table->addRow(array($value[people_id],
				$value[user_name],
				$value[lastname],
				$value[firstname],
				mailto($value[email],NULL,$value[email]),
				$value[university],
				$value[cursus]));
}

print $table;


print_footer();
?>
