<?
global $bdd;
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=ressictf;charset=utf8', 'ressictf', 'ressictf');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
} 
?> 
