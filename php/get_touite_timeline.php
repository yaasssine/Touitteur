<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";
require_once "Touite.class.php";

session_start();

if (!empty($_SESSION['id']) AND (isset($_GET['id_min']) AND $_GET['id_min'] >= 0)) {
	
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	
	$listeTimeline = $utilisateurConnecte->obtenirTimeline(intval($_GET['id_min']));
	
	if (!$listeTimeline) {
		header('Content-Type: application/json'); 
		echo json_encode(array('erreur' => true));
	}
	else {
		$listeAfficheTimeline = null;
		if (count($listeTimeline) <= 10) {
			foreach ($listeTimeline as $touite)
				$listeAfficheTimeline[] = $touite->__toString();
		}
		else {
			for ($i = 0; $i < 10; $i++)
				$listeAfficheTimeline[] = $listeTimeline[$i]->__toString();
			$listeAfficheTimeline[] = $listeTimeline[10];
		}
		header('Content-Type: application/json'); 
		echo json_encode($listeAfficheTimeline);
	}
}


?>