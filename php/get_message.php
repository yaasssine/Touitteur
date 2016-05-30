<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";
require_once "ConversationPrivee.class.php";

session_start();

if (!empty($_SESSION['id']) AND !empty($_GET['id'])) {
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	
	$conversation = new ConversationPrivee(intval($_GET['id']));
	
	$listeMessage = $conversation->obtenirMsgPriveConversation();
	
	$afficheConversation = '';
	foreach ($listeMessage as $message)
		$afficheConversation .= $message;
	
	header('Content-Type: application/json'); 
	echo json_encode($afficheConversation);
}


?>