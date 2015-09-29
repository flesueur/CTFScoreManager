<?
require_once("../includes/db.inc.php");
require_once("../includes/functions.inc.php");
require_once("../includes/printers.inc.php");

require_once("../includes/header.inc.php");

print_admin_header("Zone Admin");
?>

<br/>

<!--        <div class="pure-control-group">
             <label for="service_id">Nom de la vuln</label>
        <select id="service_id" name=service_id>
/*<?
$reponse = listServices();
foreach ($reponse as $value) {
	print("<option value=\"$value[service_id]\">$value[service_name]</option>\n");
}
?>*/
        </select>
        </div>
-->

<?
print_footer();
?>
