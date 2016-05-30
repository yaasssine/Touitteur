<?php
	try
	{
		global $bdd;
		$bdd = new PDO('mysql:host=localhost;dbname=///;charset=utf8', '///', '///');
		$bdd->query('SET NAMES utf8');
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (Exception $e)
	{
        die('<p>Échec de la connexion à la base de données</p><p>' . $e->getMessage() . '</p>');
	}
?>