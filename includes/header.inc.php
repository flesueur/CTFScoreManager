<?php
session_start();
// server should keep session data for AT LEAST 10 hours
ini_set('session.gc_maxlifetime', 36000);

// each client should remember their session id for EXACTLY 10 hours
session_set_cookie_params(36000);

reqlog();

function print_header($title="RESSI CTF 2015") {
	header('Content-Type: text/html; charset=utf-8');
echo <<< EOT
	<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!-- <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css"> -->
	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/pure-min.css" integrity="sha384-oAOxQR6DkCoMliIh8yFnu25d7Eq/PHS21PClpwjOTeU2jRSq11vu66rf90/cZr47" crossorigin="anonymous">
	<!-- <link rel="stylesheet" href="css/pure-min.css"> -->
	<link rel="stylesheet" href="css/fadeout.css">
	<title>
EOT;
	print $title;
echo <<< EOT
	</title>

</head><body>
EOT;



if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "login") {
	if (login($_POST['user_name'], $_POST['password'])) {
		print(popup("Connecté : ".getUserFromUid($_SESSION["auth_uid"])));
	} else {
		logout();
		print(popup("Échec de connexion", "red"));
	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "create_account") {
	//print_r($_POST);
	if(createAccount($_POST['user_name'], "{$_POST['part1_email']};{$_POST['part2_email']}", $_POST['password'], $_POST['password2'], $_POST['terms'],
			$_POST['part1_univ'], $_POST['part1_cursus'], $_POST['part1_firstname'], $_POST['part1_lastname'], $_POST['part1_email'],
			 $_POST['part2_univ'], $_POST['part2_cursus'],$_POST['part2_firstname'], $_POST['part2_lastname'], $_POST['part2_email'],
			$_POST['place'] )==1) {
		echo popup("Création réussie<br/>Vous pouvez maintenant vous connecter.");
	} else {
		echo popup("Échec de création, utilisateur déjà existant ou formulaire mal rempli. Merci de recommencer","red");
	}
}

if ($_SERVER['REQUEST_METHOD'] == "GET" && $_GET['action'] == "logout") {
	logout();
	print(popup("Déconnexion réussie"));
}

echo <<< EOT
<style>
/* Customization to limit height of the menu */
.custom-restricted {
background-color: #b0c4de;
  /*  height: 40px; */
}
</style>

<div class="pure-menu pure-menu-horizontal custom-restricted pure-menu-fixed">
    <a href="index.php" class="pure-menu-heading pure-menu-link">Home</a>
    <ul class="pure-menu-list">
        <li class="pure-menu-item"><a href="ranking.php" class="pure-menu-link">Classement</a></li>
	<li class="pure-menu-item"><a href="index.php?action=logout" class="pure-menu-link">Se déconnecter</a></li>
EOT;

if (isAuth() && isAdmin()) {
	echo '<li class="pure-menu-item"><a href="admin/index.php" class="pure-menu-link">Admin</a></li>';
}

echo <<< EOT
    </ul>
</div>
<br/>
<div class="content-wrapper"><div class="content">


EOT;


if (!isAuth()) {
	require("login.inc.php");
	prompt_login();
	exit;
}


}





function print_admin_header($title="ADMIN RESSI CTF 2015") {
	header('Content-Type: text/html; charset=utf-8');


if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == "login") {
	if (login($_POST['user_name'], $_POST['password'])) {
		print(popup("Connecté : ".getUserFromUid($_SESSION["auth_uid"])));
	} else {
		logout();
		print(popup("Échec de connexion", "red"));
	}
}

if (!isAuth() || !isAdmin()) {
//	require("../login.php");
	header("Location: ../index.php");
	die();
}

echo <<< EOT
	<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/pure-min.css" integrity="sha384-oAOxQR6DkCoMliIh8yFnu25d7Eq/PHS21PClpwjOTeU2jRSq11vu66rf90/cZr47" crossorigin="anonymous">
	<link rel="stylesheet" href="../css/fadeout.css">
	<title>Zone ADMIN -
EOT;
	print $title;
echo <<< EOT
	</title></head><body>
<style>
/* Customization to limit height of the menu */
.custom-restricted {
background-color: #b0c4de;
  /*  height: 40px; */
}
</style>

<div class="pure-menu pure-menu-horizontal custom-restricted pure-menu-fixed">
    <a href="../index.php" class="pure-menu-heading pure-menu-link">Home</a>
    <ul class="pure-menu-list">
        <li class="pure-menu-item"><a href="list_users.php" class="pure-menu-link">Lister les participants</a></li>
        <li class="pure-menu-item"><a href="list_services.php" class="pure-menu-link">Lister les vulns</a></li>
        <li class="pure-menu-item"><a href="list_reports.php" class="pure-menu-link">Lister les rapports</a></li>
	<li class="pure-menu-item"><a href="../index.php?action=logout" class="pure-menu-link">Se déconnecter</a></li>
    </ul>
</div>
<br/>
<div class="content-wrapper"><div class="content">
EOT;
}




function print_footer() {
/*echo "<br/>";
var_dump($_SESSION);*/
echo <<< EOT
<br/>
En cas de problème : <a href="mailto:challenge.ressi2015@utt.fr">challenge.ressi2015@utt.fr</a>
</div></body></html>
EOT;
}
?>
