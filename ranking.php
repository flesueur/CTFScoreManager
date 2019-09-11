<?php
require_once("includes/db.inc.php");
require_once("includes/functions.inc.php");
require_once("includes/printers.inc.php");

require_once("includes/header.inc.php");

print_header("Classement");
?>

<h2>Classement :</h2>

<?php
$table = new Table();
$table->setHeader(array("Rang", "Nom", "Score"));
$reponse = getRankings();
$index = 0;
foreach ($reponse as $value) {
	$table->addRow(array($index++,
				$value[user_name],
				$value[score]));
}

print $table;
?>


<h2>Vulnérabilités patchées :</h2>
<?php
$table = new Table();
$table->setHeader(array("ID", "Partiel", "Complet"));
$reponse = getSolvedServices();
$index = 0;
foreach ($reponse as $value) {
	$table->addRow(array($value[service_id],
				$value[nbpartial],
				$value[nbcomplete]));
}

print $table;
?>


<?php
print_footer();
?>
