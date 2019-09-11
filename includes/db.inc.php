<?php
global $bdd;
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=ctf;charset=utf8', 'ctf', 'ctf');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}
?>
