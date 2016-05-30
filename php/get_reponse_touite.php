<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";
require_once "Touite.class.php";

session_start();

if (!empty($_SESSION['id']) AND !empty($_GET['id']) AND (isset($_GET['id_min']) AND $_GET['id_min'] >= 0)) {
	
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	$touite = new Touite(intval($_GET['id']));
	
	$listeReponse = $touite->obtenirListeReponse(intval($_GET['id_min']));
	
	if (!$listeReponse) {
		header('Content-Type: application/json'); 
		echo json_encode(array('erreur' => true));
	}
	else {
		$listeAfficheReponse = null;
		if (count($listeReponse) <= 5) {
			foreach ($listeReponse as $touiteRep)
				$listeAfficheReponse[] = $touiteRep->__toString();
		}
		else {
			for ($i = 0; $i < 5; $i++)
				$listeAfficheReponse[] = $listeReponse[$i]->__toString();
			$listeAfficheReponse[] = $listeReponse[5];
		}
		header('Content-Type: application/json'); 
		echo json_encode($listeAfficheReponse);
	}
}


?>