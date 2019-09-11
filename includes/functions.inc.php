<?php

// TODO : tokens pour CSRF

// Fonctions internes
function login($login, $pass) {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('SELECT user_id,user_name,is_admin FROM ctf_users WHERE user_name = :user_name AND password = PASSWORD(:password)');
$req->execute(array(
    'user_name' => $login,
    'password' => $pass
    ));
	if ($row = $req->fetch()) {
		$_SESSION["isAuth"] = true;
		$_SESSION["isAdmin"] = (bool)$row["is_admin"];
		$_SESSION["auth_uid"] = $row["user_id"];
		return true;
	} else {
		return false;
	}


}

function logout() {
	unset($_SESSION["isAuth"]);
	unset($_SESSION["isAdmin"]);
	unset($_SESSION["auth_uid"]);
}

function isAuth() {
	return $_SESSION["isAuth"];
}

function isAdmin() {
	return $_SESSION["isAdmin"];
}

function popup($str,$color="green") {
	return "<div class=\"message\" style=\"background:$color;\">".$str."</div>";
}

function reqlog() {
	$bdd = $GLOBALS['bdd'];
	//if ($user_id == NULL) $user_id = $_SESSION["auth_uid"];
	if (empty($_GET) && empty($_POST) && strlen($_SERVER[REQUEST_URI]) < 40 ) return 1;
	$today = date("Y-m-d H:i:s");
	$req = $bdd->prepare('INSERT INTO ctf_log(ip_src,user_id,is_auth,is_admin,php_session,get,post,URI)
				VALUES(:ip_src,:user_id,:is_auth,:is_admin,:php_session,:get,:post,:URI)');
$err = $req->execute(array(
    'ip_src' => $_SERVER['REMOTE_ADDR'],
    'user_id' => ($_SESSION["auth_uid"])?$_SESSION["auth_uid"]:-1,
    'is_auth' => ($_SESSION["isAuth"])?$_SESSION["isAuth"]:-1,
    'is_admin' => ($_SESSION["isAdmin"])?$_SESSION["isAdmin"]:-1,
    'php_session' => json_encode($_SESSION),
    'get' => json_encode($_GET),
    'post' => json_encode($_POST),
    'URI' => json_encode($_SERVER[REQUEST_URI])
    ));
	return 1;
}

function mailto($to, $subject, $label="Mail") {
	return "<a href=\"mailto:$to?Subject=[Challenge RESSI] $subject\">$label</a>";
}

function internalref($target, $name) {
	return "<a href=\"#$target\">$name</a>";
}

function getColorCode($value) {
	switch ($value) {
		case 0: return "red";
		case 1: return "darkorange";
		case 2: return "green";
		default: return "white";
	}
}

// Get BD
// Toute sortie doit passer par xssafe (string) ou axssafe (array) pour anti XSS

function xssafe($data,$encoding='UTF-8')
{
   return htmlspecialchars($data,ENT_QUOTES | ENT_HTML401,$encoding);
}

function axssafe(&$variable) {
    foreach ($variable as &$value) {
        if (!is_array($value)) { $value = xssafe($value); }
        else { axssafe($value); }
    }
}

function listUsers() {
	$bdd = $GLOBALS['bdd'];
	$reponse = $bdd->query('SELECT user_id,user_name,email,is_admin,is_local FROM ctf_users ORDER BY is_admin,is_local DESC,user_name');
	$result = $reponse->fetchAll();
	$reponse->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function listPeople() {
	$bdd = $GLOBALS['bdd'];
	$reponse = $bdd->query('SELECT people_id,user_name,university,cursus,firstname,lastname,ctf_people.email FROM ctf_people LEFT JOIN ctf_users ON (ctf_users.user_id=ctf_people.user_id) ORDER BY user_name');
	$result = $reponse->fetchAll();
	$reponse->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function listServices() {
	$bdd = $GLOBALS['bdd'];
	$reponse = $bdd->query('SELECT service_id,service_name,service_patch FROM ctf_services ORDER BY service_id');
	$result = $reponse->fetchAll();
	$reponse->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function getServiceFromUid($uid) {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('SELECT service_name,service_patch FROM ctf_services WHERE service_id = :service_id');
	$req->execute(array(
    		'service_id' => $uid
    	));
	$result = $req->fetchAll();
	$req->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result[0];
}

function listReports() {
	$bdd = $GLOBALS['bdd'];
	$reponse = $bdd->query('SELECT
 ctf_reports.report_id,ctf_reports.user_id,ctf_reports.service_id,ctf_reports.details,ctf_services.service_name,
		ctf_users.user_name,ctf_reports.time_submitted, ctf_users.email,ctf_reports.previous_id,ctf_reports.name, 			ctf_reports.solution, ctf_reports.graded_by
 FROM ctf_reports
	INNER JOIN ctf_services ON (ctf_reports.service_id=ctf_services.service_id)
	INNER JOIN ctf_users ON (ctf_reports.user_id=ctf_users.user_id)
  ORDER BY service_id,report_id');
	$result = $reponse->fetchAll();
	$reponse->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function listUngradedReports() {
	$bdd = $GLOBALS['bdd'];
	$reponse = $bdd->query('SELECT
 ctf_reports.report_id,ctf_reports.user_id,ctf_reports.service_id,ctf_reports.details,ctf_services.service_name,
		ctf_users.user_name,ctf_reports.time_submitted, ctf_users.email,ctf_reports.previous_id,ctf_reports.name, 			ctf_reports.solution, ctf_reports.graded_by
 FROM ctf_reports
	LEFT JOIN ctf_services ON (ctf_reports.service_id=ctf_services.service_id)
	INNER JOIN ctf_users ON (ctf_reports.user_id=ctf_users.user_id)
	WHERE quick_grade=-1
  ORDER BY service_id,report_id');
	$result = $reponse->fetchAll();
	$reponse->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function listGradedReports() {
	$bdd = $GLOBALS['bdd'];
	$reponse = $bdd->query('SELECT
 ctf_reports.report_id,ctf_reports.user_id,ctf_reports.service_id,ctf_reports.details,ctf_services.service_name,
		ctf_users.user_name,ctf_reports.time_submitted, quick_grade, final_grade, time_graded,
		ctf_users.email,ctf_reports.previous_id,ctf_reports.name,
		ctf_reports.solution, ctf_reports.graded_by
 FROM ctf_reports
	LEFT JOIN ctf_services ON (ctf_reports.service_id=ctf_services.service_id)
	INNER JOIN ctf_users ON (ctf_reports.user_id=ctf_users.user_id)
	WHERE quick_grade!=-1
  ORDER BY service_id,quick_grade,report_id');
	$result = $reponse->fetchAll();
	$reponse->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function listMyReports() {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('SELECT
 ctf_reports.report_id,ctf_reports.user_id,ctf_reports.service_id,ctf_reports.details,ctf_reports.quick_grade, ctf_reports.final_grade,ctf_services.service_name, ctf_reports.previous_id, ctf_reports.time_submitted,
	ctf_reports.name, ctf_reports.solution
 FROM ctf_reports
	LEFT JOIN ctf_services ON (ctf_reports.service_id=ctf_services.service_id)
  WHERE ctf_reports.user_id=:user
  ORDER BY service_id,report_id');
	$req->execute(array(
    		'user' => $_SESSION["auth_uid"]
    	));
	$result = $req->fetchAll();
	$req->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function getUserFromUid ($uid){
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('SELECT user_name FROM ctf_users WHERE user_id = :uid');
	$req->execute(array(
    		'uid' => $uid
    	));
	if ($row = $req->fetch()) {
		return xssafe($row["user_name"]);
	}
	return NULL;
}

function getAccountFromUid($uid) {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('SELECT user_id,user_name,email,password,is_admin,is_local FROM ctf_users WHERE user_id = :user_id');
	$req->execute(array(
    		'user_id' => $uid
    	));
	$result = $req->fetchAll();
	$req->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result[0];
}

/* function getScore($user_id) {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('SELECT SUM(Max)
	FROM
	(
	SELECT MAX(quick_grade) as \'Max\'
	 FROM ctf_reports
  	WHERE ctf_reports.user_id=:user
  	GROUP BY service_id
	) as T');
	$req->execute(array(
    		'user' => $user_id
    	));
	$result = $req->fetchAll();
	$req->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result[0];
} */

function getRankings() {
	$bdd = $GLOBALS['bdd'];
	/*$req = $bdd->prepare('SELECT user_id, COALESCE(SUM(Max), 0) as score, user_name
FROM
(SELECT ctf_users.user_id, user_name, service_id, MAX(quick_grade) as Max
 FROM ctf_users
  LEFT JOIN ctf_reports ON (ctf_reports.user_id = ctf_users.user_id)
  GROUP BY user_id, service_id
) AS T
GROUP BY user_id
ORDER BY score DESC');*/
	$req = $bdd->prepare('SELECT ctf_users.user_id, score, user_name
FROM ctf_users
INNER JOIN ctf_v_scores ON ctf_v_scores.user_id = ctf_users.user_id
ORDER BY score DESC, user_name');
	$req->execute();
	$result = $req->fetchAll();
	$req->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function getPartiallySolved() {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('SELECT ctf_services.service_id, COALESCE(nbquick, 0) as nbquick
FROM ctf_services
LEFT JOIN
(
SELECT service_id, COUNT(*) as nbquick
FROM
(
SELECT ctf_reports.service_id, user_id
FROM ctf_reports
WHERE quick_grade = 1
  GROUP BY service_id, user_id
) AS T
GROUP BY service_id
) AS S
ON S.service_id = ctf_services.service_id');
	$req->execute();
	$result = $req->fetchAll();
	$req->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}

function getSolvedServices() {
	$bdd = $GLOBALS['bdd'];
/*	$req = $bdd->prepare('SELECT ctf_services.service_id, COALESCE(S.nbpartial, 0) as nbpartial, COALESCE(S2.nbcomplete, 0) as nbcomplete
FROM ctf_services
LEFT JOIN
(
SELECT service_id, COUNT(*) as nbpartial
FROM
(
SELECT ctf_reports.service_id, user_id
FROM ctf_reports
WHERE quick_grade = 1
  GROUP BY service_id, user_id) AS T

GROUP BY service_id
) AS S
ON S.service_id = ctf_services.service_id

LEFT JOIN
(
SELECT service_id, COUNT(*) as nbcomplete
FROM
(
SELECT ctf_reports.service_id, user_id
FROM ctf_reports
WHERE quick_grade = 2
  GROUP BY service_id, user_id) AS T2

GROUP BY service_id
) AS S2
ON S2.service_id = ctf_services.service_id
WHERE ctf_services.service_id != -1
');*/
	$req = $bdd->prepare('SELECT service_id, nbcomplete, nbpartial
FROM ctf_v_servicesstats
WHERE service_id != -1
ORDER BY service_id');
	$req->execute();
	$result = $req->fetchAll();
	$req->closeCursor(); // Termine le traitement de la requête
	axssafe($result);
	return $result;
}


// Put BD
// Tout passe à travers de PDO pour éviter les injections
// Les filter_var sont a priori inutiles, je n'en mets plus

// Account mgmt
function createAccount($user_name, $email, $password, $password2, $terms,
			$part1_univ, $part1_cursus, $part1_firstname, $part1_lastname, $part1_email,
			$part2_univ, $part2_cursus, $part2_firstname, $part2_lastname, $part2_email,
			$place) {
	if ($terms != "on") return 0;
	if ($password != $password2) return 0;
	if (strlen($user_name) < 1) return 0;
	if (strlen($part1_email) < 3) return 0;
	if (!filter_var($part1_email, FILTER_VALIDATE_EMAIL)) {
	  return 0;
	}

	$isLocal = false;
	if ($place == "local") $isLocal = true;
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('INSERT INTO ctf_users(user_name,email,password,is_local) VALUES(:user_name, :email, PASSWORD(:password), :place)');
$err = $req->execute(array(
    'user_name' => filter_var($user_name,FILTER_SANITIZE_STRING),
    'email' => filter_var($part1_email,FILTER_SANITIZE_EMAIL).";".filter_var($part2_email,FILTER_SANITIZE_EMAIL),
    'password' => $password,
    'place' => $isLocal
    ));
//filter_var($email,FILTER_SANITIZE_STRING),
	$user_id = $bdd->lastInsertId();

	if ($err !=1 ) return 0;

	$req = $bdd->prepare('INSERT INTO ctf_people(user_id,university,cursus,firstname,lastname,email) VALUES(:user_id, :university, :cursus, :firstname, :lastname, :email)');
 	$req->execute(array(
    'user_id' => $user_id,
    'university' => filter_var($part1_univ,FILTER_SANITIZE_STRING),
    'cursus' => filter_var($part1_cursus,FILTER_SANITIZE_STRING),
    'firstname' => filter_var($part1_firstname,FILTER_SANITIZE_STRING),
    'lastname' => filter_var($part1_lastname,FILTER_SANITIZE_STRING),
    'email' => filter_var($part1_email,FILTER_SANITIZE_EMAIL)
    ));

	 $req->execute(array(
    'user_id' => $user_id,
    'university' => filter_var($part2_univ,FILTER_SANITIZE_STRING),
    'cursus' => filter_var($part2_cursus,FILTER_SANITIZE_STRING),
    'firstname' => filter_var($part2_firstname,FILTER_SANITIZE_STRING),
    'lastname' => filter_var($part2_lastname,FILTER_SANITIZE_STRING),
    'email' => filter_var($part2_email,FILTER_SANITIZE_EMAIL)
    ));

	return $err;
}

function deleteAccount($user_id) {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('DELETE FROM ctf_users WHERE user_id = :user_id');
$err = $req->execute(array(
    'user_id' => $user_id
    ));
	return $err;
}

function updateAccount($user_id, $user_name, $email, $password, $isAdmin, $isLocal) {

	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('UPDATE ctf_users SET user_name=:user_name,email=:email,password=:password,is_local=:is_local,is_admin=:is_admin WHERE user_id = :user_id');
$err = $req->execute(array(
    'user_name' => filter_var($user_name,FILTER_SANITIZE_STRING),
    'email' => filter_var($email,FILTER_SANITIZE_EMAIL),
    'password' => $password,
    'is_local' => $isLocal,
'is_admin' => $isAdmin,
'user_id' => $user_id
    ));
	return $err;
}

// Report mgmt
function createReport($service_id, $details, $user_id, $previous_id, $name="", $solution="") {
	$bdd = $GLOBALS['bdd'];
	if ($user_id == NULL) $user_id = $_SESSION["auth_uid"];
	$today = date("Y-m-d H:i:s");
	$req = $bdd->prepare('INSERT INTO ctf_reports(user_id,service_id,details,time_submitted, previous_id, name, solution) VALUES(:user, :service, :details, :time, :previd, :name, :solution)');
$err = $req->execute(array(
    'user' => $user_id,
    'service' => $service_id,
    'details' => filter_var($details,FILTER_SANITIZE_STRING),
    'time' => $today,
    'previd' => $previous_id,
    'name' => $name,
    'solution' => $solution
    ));
	return $err;
}

function updateReport($report_id, $service_id, $quick_grade) {
	$bdd = $GLOBALS['bdd'];
	$today = date("Y-m-d H:i:s");
	$req = $bdd->prepare('UPDATE ctf_reports SET service_id=:service_id,quick_grade=:quick_grade,time_graded=:time,graded_by=:graded_by WHERE report_id = :report_id');
$err = $req->execute(array(
    'service_id' => $service_id,
    'quick_grade' => $quick_grade,
    'report_id' => $report_id,
    'time' => $today,
    'graded_by' => $_SESSION["auth_uid"]
    ));
	return $err;
}


// Vuln mngmt
function createVuln($service_name, $service_patch) {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('INSERT INTO ctf_services(service_name,service_patch) VALUES(:name, :patch)');
$err = $req->execute(array(
    'name' => $service_name,
    'patch' => $service_patch
    ));
	return $err;
}

function deleteVuln($service_id) {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('DELETE FROM ctf_services WHERE service_id = :id');
$err = $req->execute(array(
    'id' => $service_id
    ));
	return $err;
}

function updateVuln($service_id, $service_name, $service_patch) {
	$bdd = $GLOBALS['bdd'];
	$req = $bdd->prepare('UPDATE ctf_services SET service_name=:service_name,service_patch=:service_patch WHERE service_id = :service_id');
$err = $req->execute(array(
    'service_name' => $service_name,
    'service_patch' => $service_patch,
    'service_id' => $service_id
    ));
	return $err;
}





?>
