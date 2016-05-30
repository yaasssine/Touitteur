<?php
require_once "connexion.php";

session_start();

if (!empty(trim($_POST['id']))) {
	$id = intval($_POST['id']);
	
	try {
		$req = $bdd->prepare('DELETE FROM Suivre WHERE idDemandeur=:idD AND idReceveur=:idR');
		$req->bindValue(':idD', $_SESSION['id']);
		$req->bindValue(':idR', $id);
		$req->execute();
		header('Content-Type: application/json'); 
		echo json_encode(array('etat' => true));
	} catch (Exception $e) {
		header('Content-Type: application/json'); 
		echo json_encode(array('etat' => false));
	}
}

?>