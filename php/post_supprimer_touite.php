<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";
require_once "Touite.class.php";
require_once "TouiteReponse.class.php";

session_start();

if (!empty($_SESSION['id']) AND !empty($_POST['id_touite']) AND !empty($_POST['touite_reponse'])) {
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	
	if ($_POST['touite_reponse'] == 'false')
		$touite = new Touite(intval($_POST['id_touite']));
	else
		$touite = new TouiteReponse(intval($_POST['id_touite']));
	
	$suppression = false;
	
	$suppression = $touite->supprimerTouite();
	
	if ($suppression) {
		header('Content-Type: application/json'); 
		echo json_encode(array('afficherTouite' => html_entity_decode($touite->__toString())));
	}
	else {
		header('Content-Type: application/json');
		echo json_encode(array('erreur' => true, 'erreurMsg' => "Ce touite ne peut pas être supprimé."));
	}
}

?>