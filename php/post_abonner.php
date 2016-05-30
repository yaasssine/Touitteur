<?php
require_once "connexion.php";

session_start();

if (!empty(trim($_POST['id']))) {
	$id = intval($_POST['id']);
	
	try {
		$req = $bdd->prepare('INSERT INTO Suivre VALUES(:idD, :idR, "V")');
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