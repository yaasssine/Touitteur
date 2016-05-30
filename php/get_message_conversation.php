<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";
require_once "ConversationPrivee.class.php";

session_start();

if (!empty($_SESSION['id']) AND !empty($_GET['id'])) {
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	
	$conversation = new ConversationPrivee(intval($_GET['id']));
	
	$afficheConversation = '<h1>' . $conversation->getDestinataire()->getPseudo() . '<button class="message retour">Retour</button></h1>';
	
	$listeMessage = $conversation->obtenirMsgPriveConversation();
	
	$afficheConversation .= '<ul id="convers" class="conversation">';
	if (!$listeMessage) {
		$afficheConversation .= '<li></li>';
	}
	else { 
		foreach ($listeMessage as $message)
			$afficheConversation .= $message;
	}
	$afficheConversation .= '</ul>';
	
	$afficheConversation .= '<div class="zone-envoi-message" data-id-destinataire="' . $conversation->getDestinataire()->getId() . '" style="clear: both; border-top: 1px solid #e8e8e8;">';
	
	$afficheConversation .= '<aside style="padding: 10px 20px 0 0;"><button id="envoyer-message">Envoyer</button><span id="compteur-envoyer-message">140</span></aside>';
	
	$afficheConversation .= '<p><textarea id="texte-envoyer-message" name="message" placeholder="Message à envoyer à @' . $conversation->getDestinataire()->getPseudo() . '" maxlength="140" style="width: 340px; margin-right: 10px;"></textarea></p>';
	
	$afficheConversation .= '</div>';
	
	$conversation->setVuConversation();
	
	header('Content-Type: application/json'); 
	echo json_encode($afficheConversation);
}


?>