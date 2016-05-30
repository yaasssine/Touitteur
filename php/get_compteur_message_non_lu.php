<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";

session_start();

if (!empty($_SESSION['id'])) {
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	
	header('Content-Type: application/json'); 
	echo json_encode($utilisateurConnecte->obtenirNbMsgPriveNonLu());
}

?>