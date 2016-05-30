<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";

session_start();

if (!empty($_SESSION['id'])) {
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	
	$afficheConversation = '<h1>Messages privés</h1>';

	$listeConversation = $utilisateurConnecte->obtenirListeMsgPrive();
	
	if (!$listeConversation)
		$afficheConversation .= '<p>Vous n\'avez reçu aucun message privé.</p>';
	else {
		$afficheConversation .= '<ul class="liste-msg">';
		foreach ($listeConversation as $conversation) {
			$afficheConversation .= '<li>';
			$afficheConversation .= $conversation;
			$afficheConversation .= '</li>';
		}
	}
	
	header('Content-Type: application/json'); 
	echo json_encode($afficheConversation);
}


?>