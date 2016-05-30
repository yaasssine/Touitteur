<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";

session_start();

if (!empty($_SESSION['id']) AND !empty($_POST['id']) AND !empty($_POST['texte'])) {
	
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	
	$texte = htmlentities($_POST['texte']);
	$conversation = new ConversationPrivee (intval($_POST['id']));
	
	if ($conversation->envoyerMessage($texte)) {
		header('Content-Type: application/json');
		echo json_encode('<li class="utilisateur"><p>' . $texte . '</p></li>');
	}
	else {
		header('Content-Type: application/json');
		echo json_encode(array('erreur' => true, 'erreurMsg' => "Une erreur s'est produite. Le message n'a pas été envoyé."));
	}
}

?>