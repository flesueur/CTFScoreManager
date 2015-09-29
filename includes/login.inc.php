<?
function prompt_login() {

echo <<< EOT
<br/><br/>
<div class="pure-g">
    <div class="pure-u-1-2">
<h2>Login</h2>
EOT;

$formular = new Formular("index.php", "POST");
$formular->addHidden("action", "login");
$formular->addText("user_name", "Login", "Login", NULL);
$formular->addPassword("password", "Password", "Password", NULL);
$formular->addSubmitButton();
print $formular;

echo <<< EOT
</div>
    <div class="pure-u-1-2">
<h2>Créer un compte</h2>
EOT;

$formular = new Formular("index.php", "POST");
$formular->addHidden("action", "create_account");
$formular->addText("user_name", "Nom de l'équipe", "Nom de l'équipe", $_POST[user_name]);
$formular->addPassword("password", "Password", "Password", $_POST[password]);
$formular->addPassword("password2", "Confirmation du password", "Password", $_POST[password2]);
$formular->addLabel("Participant 1 :");
$formular->addText("part1_firstname", "Prénom", "Prénom", $_POST[part1_firstname]);
$formular->addText("part1_lastname", "Nom", "Nom", $_POST[part1_lastname]);
$formular->addText("part1_email", "Adresse email", "Adresse email", $_POST[part1_email]);
$formular->addText("part1_univ", "Université/École", "Université/École", $_POST[part1_univ]);
$formular->addText("part1_cursus", "Cursus/Année", "Cursus/Année", $_POST[part1_cursus]);
$formular->addLabel("Participant 2 :");
$formular->addText("part2_firstname", "Prénom", "Prénom", $_POST[part2_firstname]);
$formular->addText("part2_lastname", "Nom", "Nom", $_POST[part2_lastname]);
$formular->addText("part2_email", "Adresse email", "Adresse email", $_POST[part2_email]);
$formular->addText("part2_univ", "Université/École", "Université/École", $_POST[part2_univ]);
$formular->addText("part2_cursus", "Cursus/Année", "Cursus/Année", $_POST[part2_cursus]);
$formular->addRadio("place", "local", array("local"=>"Sur place","remote"=>"À distance"));
$formular->addCheckbox("terms", "J'ai lu et j'accepte le règlement");
$formular->addSubmitButton();
print $formular;


echo <<< EOT
</div>
</div>
EOT;
}
?>

